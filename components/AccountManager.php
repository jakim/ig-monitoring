<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\components;


use app\components\http\Client;
use app\models\Account;
use app\models\AccountStats;
use app\models\AccountTag;
use app\models\Media;
use app\models\Proxy;
use app\models\Tag;
use jakim\ig\Endpoint;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\di\Instance;
use yii\helpers\Json;

class AccountManager extends Component
{
    /**
     * @var Proxy
     */
    public $proxy;

    /**
     * @var \yii\caching\Cache
     */
    public $cache = 'cache';

    public function init()
    {
        parent::init();
        if ($this->cache !== false) {
            $this->cache = Instance::ensure($this->cache, Cache::class);
        }
    }

    /**
     * @param \app\models\Account $account
     * @param string[] $tags
     * @throws \Throwable
     */
    public function updateTags(Account $account, array $tags)
    {
        // clearing
        AccountTag::deleteAll(['account_id' => $account->id]);

        // add
        foreach ($tags as $tag) {
            $model = Tag::findOne(['name' => $tag]);
            if ($model === null && is_string($tag)) {
                $model = new Tag(['name' => $tag]);
                $model->insert();
            }
            $account->link('tags', $model);
        }
    }

    /**
     * @param \app\models\Account $account
     * @return array
     */
    public function fetchDetails(Account $account): array
    {
        $url = Endpoint::accountDetails($account->username);

        if ($this->cache === false) {
            return $this->fetchContent($url, $account);
        }

        return $this->cache->getOrSet([$url], function ($cache) use ($url, $account) {
            return $this->fetchContent($url, $account);
        }, 3600);
    }

    /**
     * Fetch data from API, update details and stats.
     *
     * @param \app\models\Account $account
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function update(Account $account)
    {
        $content = $this->fetchDetails($account);
        $this->updateDetails($account, $content);
        $this->updateStats($account, $content);
    }

    /**
     * @param \app\models\Account $account
     * @param array $content
     * @return \app\models\Account
     * @throws \yii\base\InvalidConfigException
     */
    public function updateDetails(Account $account, array $content = []): Account
    {
        $content = $content ?: $this->fetchDetails($account);

        $accountData = ArrayHelper::arrayMap($content, [
            'profile_pic_url' => 'user.profile_pic_url',
            'full_name' => 'user.full_name',
            'biography' => 'user.biography',
            'external_url' => 'user.external_url',
            'instagram_id' => 'user.id',
        ]);

        $account->attributes = $accountData;
        $this->updateProfilePic($account);

        $account->save();

        return $account;
    }

    /**
     * @param \app\models\Account $account
     * @param array $content
     * @return \app\models\AccountStats|null
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function updateStats(Account $account, array $content = []): ?AccountStats
    {
        $content = $content ?: $this->fetchDetails($account);

        $statsData = ArrayHelper::arrayMap($content, [
            'followed_by' => 'user.followed_by.count',
            'follows' => 'user.follows.count',
            'media' => 'user.media.count',
        ]);

        $accountStats = new AccountStats($statsData);


        if ($this->statsNeedUpdate($account, $accountStats)) {
            $account->link('accountStats', $accountStats);
            $account->resetStatsCache();
        }
        $this->updateMedia($account, $content);
        $this->updateEr($account);

        return !$accountStats->isNewRecord ? $accountStats : null;
    }

    public function saveUsernames(array $usernames)
    {
        $createdAt = (new \DateTime())->format('Y-m-d H:i:s');
        $rows = array_map(function ($username) use ($createdAt) {
            return [
                $username,
                $createdAt,
                $createdAt,
            ];
        }, $usernames);

        $sql = \Yii::$app->db->getQueryBuilder()
            ->batchInsert(Account::tableName(), ['username', 'updated_at', 'created_at'], $rows);
        $sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);
        \Yii::$app->db->createCommand($sql)
            ->execute();
    }

    protected function updateEr(Account $account, int $mediaLimit = 10): bool
    {
        if (!$account->lastAccountStats) {
            return false;
        }

        $media = Media::find()
            ->innerJoinWith('mediaStats')
            ->andWhere(['account_id' => $account->id])
            ->limit($mediaLimit)
            ->groupBy('media.id')
            ->all();

        $er = [];
        foreach ($media as $m) {
            $er[] = ($m->lastMediaStats->likes + $m->lastMediaStats->comments) / $m->lastMediaStats->account_followed_by;
        }

        $er = $er ? array_sum($er) / count($er) : 0;
        $account->lastAccountStats->er = round($er, 4);

        return $account->lastAccountStats->update();
    }

    /**
     * @param \app\models\Account $account
     * @param array $content
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function updateMedia(Account $account, array $content)
    {
        $manager = \Yii::createObject([
            'class' => MediaManager::class,
            'account' => $account,
        ]);

        $items = ArrayHelper::getValue($content, 'user.media.nodes', []);

        foreach ($items as $item) {
            $id = ArrayHelper::getValue($item, 'id');
            $media = Media::findOne(['instagram_id' => $id]);
            if ($media === null) {
                $media = new Media(['account_id' => $account->id]);
            }
            $manager->update($media, $item);
        }
    }

    public function updateMediaHistory(Account $account)
    {
        $url = Endpoint::accountMedia($account->instagram_id, 200);
        $content = $this->fetchContent($url, $account);

        $items = ArrayHelper::getValue($content, 'data.user.edge_owner_to_timeline_media.edges', []);

        $manager = \Yii::createObject([
            'class' => MediaManager::class,
            'account' => $account,
            'propertyMap' => MediaManager::PROPERTY_MAP_ACCOUNT_MEDIA,
        ]);

        foreach ($items as $item) {
            $id = ArrayHelper::getValue($item, 'node.id');
            $media = Media::findOne(['instagram_id' => $id]);
            if ($media === null) {
                $media = new Media(['account_id' => $account->id]);
            }
            $manager->update($media, $item);
        }
    }

    /**
     * @param \app\models\Account $account
     * @return \app\models\Proxy
     * @throws \yii\base\InvalidConfigException
     */
    protected function getProxy(Account $account): Proxy
    {
        $proxy = $this->proxy ?: $account->proxy;
        if (!$proxy || !$proxy->active) {
            throw new InvalidConfigException('Account proxy must be set and active.');
        }

        return $proxy;
    }

    protected function fetchContent($url, Account $account): array
    {
        $proxy = $this->getProxy($account);

        $client = Client::factory($proxy);
        $res = $client->get($url);

        return Json::decode($res->getBody()->getContents());
    }

    /**
     * @param \app\models\Account $account
     * @throws \yii\base\InvalidConfigException
     */
    protected function updateProfilePic(Account $account): void
    {
        if ($account->profile_pic_url) {
            $url = $account->profile_pic_url;
            $account->profile_pic_url = null;

            $filename = sprintf('%s_%s', $account->username, basename($url));
            $path = \Yii::getAlias("@app/web/uploads/{$filename}");
            $fileExists = file_exists($path);

            if (!$fileExists) {
                $proxy = $this->getProxy($account);
                $client = Client::factory($proxy);
                $content = $client->get($url)->getBody()->getContents();
                if ($content && file_put_contents($path, $content)) {
                    $account->profile_pic_url = "/uploads/{$filename}";
                }
            } elseif ($fileExists) {
                $account->profile_pic_url = "/uploads/{$filename}";
            }
        }
    }

    protected function statsNeedUpdate(Account $account, AccountStats $freshAccountStats): bool
    {
        if (!$account->lastAccountStats) {
            return true;
        }

        return $account->lastAccountStats->followed_by != $freshAccountStats->followed_by ||
            $account->lastAccountStats->follows != $freshAccountStats->follows ||
            $account->lastAccountStats->media != $freshAccountStats->media;
    }
}