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
use app\models\MediaStats;
use app\models\Proxy;
use app\models\Tag;
use jakim\ig\Endpoint;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\helpers\StringHelper;

class AccountManager extends Component
{
    /**
     * @var Proxy
     */
    public $proxy;

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
     * @throws \yii\base\InvalidConfigException
     */
    public function fetchDetails(Account $account): array
    {
        $proxy = $this->getProxy($account);
        $client = Client::factory($proxy);

        $url = (new Endpoint())->accountDetails($account->username);
        $res = $client->get($url);
        $content = Json::decode($res->getBody()->getContents());

        return $content;
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

        // if first run or something change
        if (
            !$account->lastAccountStats ||
            (
                $account->lastAccountStats &&
                (
                    $account->lastAccountStats->followed_by != $statsData['followed_by'] ||
                    $account->lastAccountStats->follows != $statsData['follows'] ||
                    $account->lastAccountStats->media != $statsData['media']
                )
            )
        ) {
            $accountStats = new AccountStats();
            $accountStats->attributes = $statsData;
            $account->link('accountStats', $accountStats);
            $account->refresh();
        }

        $this->updateMedia($account, $content);

        if (isset($accountStats)) {
            $accountStats->er = $this->calculateEr($account);
            $accountStats->save();
        }

        return $accountStats ?? null;
    }

    /**
     * @param \app\models\Account $account
     * @param int $mediaLimit
     * @return float|int
     */
    public function calculateEr(Account $account, int $mediaLimit = 10)
    {
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

        return round($er, 2);
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

    /**
     * @param \app\models\Account $account
     * @return \app\models\Proxy
     * @throws \yii\base\InvalidConfigException
     */
    protected function getProxy(Account $account): Proxy
    {
        $proxy = $this->proxy ?: $account->proxy;
        if ($proxy === null || !$proxy->active) {
            throw new InvalidConfigException('Account proxy must be set and active.');
        }

        return $proxy;
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
}