<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\components;


use app\components\http\Client;
use app\components\http\CacheStorage;
use app\models\Account;
use app\models\AccountStats;
use app\models\AccountTag;
use app\models\Media;
use app\models\Proxy;
use app\models\Tag;
use GuzzleHttp\HandlerStack;
use Jakim\Query\AccountQuery;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use yii\base\Component;
use yii\base\InvalidConfigException;

class AccountManager extends Component
{
    /**
     * @var Proxy
     */
    public $proxy;

    public function fetchDetails(Account $account): \Jakim\Model\Account
    {
        $query = $this->queryFactory($account);

        return $query->findOne($account->username);
    }

    /**
     * Fetch data from API, update details and stats.
     *
     * @param \app\models\Account $account
     * @return \app\models\Account
     */
    public function update(Account $account)
    {
        $data = $this->fetchDetails($account);
        $this->updateDetails($account, $data);
        $this->updateStats($account, $data);

        return $account;
    }

    public function updateDetails(Account $account, \Jakim\Model\Account $data = null): Account
    {
        $data = $data ?: $this->fetchDetails($account);

        $account->profile_pic_url = $data->profilePicUrl;
        $account->full_name = $data->fullName;
        $account->biography = $data->biography;
        $account->external_url = $data->externalUrl;
        $account->instagram_id = $data->id;

        $this->updateProfilePic($account);

        $account->save();

        return $account;
    }

    public function updateStats(Account $account, \Jakim\Model\Account $data = null): ?AccountStats
    {
        $data = $data ?: $this->fetchDetails($account);
        $accountStats = null;

        if ($this->statsNeedUpdate($account, $data)) {
            $accountStats = new AccountStats();
            $accountStats->followed_by = $data->followedBy;
            $accountStats->follows = $data->follows;
            $accountStats->media = $data->media;
            $account->link('accountStats', $accountStats);
            $account->resetStatsCache();
        }
        $this->updateMedia($account);
        $this->updateEr($account);

        return $accountStats;
    }

    protected function updateEr(Account $account, int $mediaLimit = 10)
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

    protected function updateMedia(Account $account)
    {
        $manager = \Yii::createObject([
            'class' => MediaManager::class,
            'account' => $account,
        ]);

        $query = $this->queryFactory($account);
        $items = $query->findPosts($account->username);

        foreach ($items as $item) {
            $media = Media::findOne(['instagram_id' => $item->id]);
            if ($media === null) {
                $media = new Media(['account_id' => $account->id]);
            }
            $manager->update($media, $item);
        }
    }

    public function updateTags(Account $account, array $tags)
    {
        // clearing
        AccountTag::deleteAll(['account_id' => $account->id]);

        // add
        foreach (array_filter($tags) as $tag) {
            $model = Tag::findOne(['name' => $tag]);
            if ($model === null && $tag) {
                $model = new Tag(['name' => $tag]);
                $model->insert();
            }
            $account->link('tags', $model);
        }
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

//    public function updateMediaHistory(Account $account)
//    {
//        $url = Endpoint::accountMedia($account->instagram_id, 200);
//        $content = $this->fetchContent($url, $account);
//
//        $items = ArrayHelper::getValue($content, 'data.user.edge_owner_to_timeline_media.edges', []);
//        $items = ArrayHelper::index($items, 'node.id');
//
//        $manager = \Yii::createObject([
//            'class' => MediaManager::class,
//            'account' => $account,
//            'propertyMap' => MediaManager::PROPERTY_MAP_ACCOUNT_MEDIA,
//        ]);
//
//        $this->internalUpdateMedia($account, $items, $manager);
//    }

    protected function getProxy(Account $account): Proxy
    {
        $proxy = $this->proxy ?: $account->proxy;
        if (!$proxy || !$proxy->active) {
            throw new InvalidConfigException('Account proxy must be set and be active.');
        }

        return $proxy;
    }

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

    private function statsNeedUpdate(Account $account, \Jakim\Model\Account $data): bool
    {
        if (!$account->lastAccountStats) {
            return true;
        }

        return $account->lastAccountStats->followed_by != $data->followedBy ||
            $account->lastAccountStats->follows != $data->follows ||
            $account->lastAccountStats->media != $data->media;
    }

    /**
     * @param \app\models\Account $account
     * @return AccountQuery
     * @throws \yii\base\InvalidConfigException
     */
    private function queryFactory(Account $account): AccountQuery
    {
        $proxy = $this->getProxy($account);

        $stack = HandlerStack::create();
        $stack->push(new CacheMiddleware(
            new GreedyCacheStrategy(
                new CacheStorage(), 3600)
        ), 'cache');
        $client = Client::factory($proxy, ['handler' => $stack]);

        $query = new AccountQuery($client);

        return $query;
    }
}