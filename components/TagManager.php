<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\components;


use app\components\http\CacheStorage;
use app\components\http\Client;
use app\models\Proxy;
use app\models\Tag;
use app\models\TagStats;
use GuzzleHttp\HandlerStack;
use Jakim\Query\TagQuery;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\helpers\Json;

class TagManager extends Component
{
    /**
     * @var \app\models\Proxy
     */
    public $proxy;

    /**
     * @var bool
     */
    public $cache = true;

    public function fetchDetails(Tag $tag): \Jakim\Model\Tag
    {
        $query = $this->queryFactory($tag);

        return $query->findOne($tag->name);
    }

    public function update(Tag $tag)
    {
        $data = $this->fetchDetails($tag);
        $this->updateStats($tag, $data);

        return $tag;
    }

    public function updateStats(Tag $tag, \Jakim\Model\Tag $data = null): ?TagStats
    {
        $data = $data ?: $this->fetchDetails($tag);
        $tagStats = null;

        if ($this->statsNeedUpdate($tag, $data)) {
            $tagStats = new TagStats();
            $tagStats->media = $data->media;
            $tagStats->likes = $data->likes;
            $tagStats->min_likes = $data->minLikes;
            $tagStats->max_likes = $data->maxLikes;
            $tagStats->comments = $data->comments;
            $tagStats->min_comments = $data->minComments;
            $tagStats->max_comments = $data->maxComments;
            $tag->link('tagStats', $tagStats);
        }

        return $tagStats;
    }

    private function statsNeedUpdate(Tag $tag, \Jakim\Model\Tag $data): bool
    {
        if (!$tag->lastTagStats) {
            return true;
        }

        return $tag->lastTagStats->media != $data->media ||
            $tag->lastTagStats->likes != $data->likes ||
            $tag->lastTagStats->comments != $data->comments ||
            $tag->lastTagStats->min_likes != $data->minLikes ||
            $tag->lastTagStats->max_likes != $data->maxLikes ||
            $tag->lastTagStats->min_comments != $data->minComments ||
            $tag->lastTagStats->max_comments != $data->maxComments;

    }

    /**
     * @param string[] $tags Names array
     * @throws \yii\db\Exception
     */
    public function saveTags(array $tags)
    {
        $createdAt = (new \DateTime())->format('Y-m-d H:i:s');
        $rows = array_map(function($tag) use ($createdAt) {
            return [
                $tag,
                Inflector::slug($tag),
                $createdAt,
                $createdAt,
            ];
        }, $tags);

        $sql = \Yii::$app->db->getQueryBuilder()
            ->batchInsert(Tag::tableName(), ['name', 'slug', 'updated_at', 'created_at'], $rows);
        $sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);
        \Yii::$app->db->createCommand($sql)
            ->execute();
    }

    /**
     * @param \app\models\Tag $tag
     * @return \app\models\Proxy
     * @throws \yii\base\InvalidConfigException
     */
    protected function getProxy(Tag $tag): Proxy
    {
        $proxy = $this->proxy ?: $tag->proxy;
        if ($proxy === null || !$proxy->active) {
            throw new InvalidConfigException('Tag proxy must be set and active.');
        }

        return $proxy;
    }

    /**
     * @param \app\models\Tag $tag
     * @return \Jakim\Query\TagQuery
     * @throws \yii\base\InvalidConfigException
     */
    private function queryFactory(Tag $tag): TagQuery
    {
        $proxy = $this->getProxy($tag);

        if ($this->cache) {
            $stack = HandlerStack::create();
            $stack->push(new CacheMiddleware(
                new GreedyCacheStrategy(
                    new CacheStorage(), 3600)
            ), 'cache');
            $client = Client::factory($proxy, ['handler' => $stack]);
        } else {
            $client = Client::factory($proxy);
        }

        $query = new TagQuery($client);

        return $query;
    }
}