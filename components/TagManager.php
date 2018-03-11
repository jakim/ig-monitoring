<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\components;


use app\components\http\Client;
use app\models\Proxy;
use app\models\Tag;
use app\models\TagStats;
use jakim\ig\Endpoint;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\di\Instance;
use yii\helpers\Inflector;
use yii\helpers\Json;

class TagManager extends Component
{
    /**
     * @var \app\models\Proxy
     */
    public $proxy;

    /**
     * @var Cache
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
     * @param \app\models\Tag $tag
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function fetchDetails(Tag $tag): array
    {
        $url = Endpoint::exploreTags($tag->name);

        if ($this->cache === false) {
            return $this->fetchContent($url, $tag);
        }

        return $this->cache->getOrSet([$url], function() use ($url, $tag) {
            return $this->fetchContent($url, $tag);
        }, 3600);

    }

    /**
     * @param \app\models\Tag $tag
     * @throws \yii\base\InvalidConfigException
     */
    public function update(Tag $tag)
    {
        $content = $this->fetchDetails($tag);
        $this->updateStats($tag, $content);
    }

    /**
     * @param \app\models\Tag $tag
     * @param array $content
     * @return \app\models\TagStats|null
     * @throws \yii\base\InvalidConfigException
     */
    public function updateStats(Tag $tag, array $content = []): ?TagStats
    {
        $content = $content ?: $this->fetchDetails($tag);

        $likes = ArrayHelper::getColumn(ArrayHelper::getValue($content, 'graphql.hashtag.edge_hashtag_to_top_posts.edges'), 'node.edge_liked_by.count');
        sort($likes, SORT_ASC);
        $comments = ArrayHelper::getColumn(ArrayHelper::getValue($content, 'graphql.hashtag.edge_hashtag_to_top_posts.edges'), 'node.edge_media_to_comment.count');
        sort($comments, SORT_ASC);
        $statsData = [
            'media' => ArrayHelper::getValue($content, 'graphql.hashtag.edge_hashtag_to_media.count'),
            'likes' => array_sum($likes),
            'comments' => array_sum($comments),
            'min_likes' => $likes['0'],
            'max_likes' => end($likes),
            'min_comments' => $comments['0'],
            'max_comments' => end($comments),
        ];

        if (
            $tag->lastTagStats &&
            $tag->lastTagStats->media == $statsData['media'] &&
            $tag->lastTagStats->likes == $statsData['likes'] &&
            $tag->lastTagStats->comments == $statsData['comments'] &&
            $tag->lastTagStats->min_likes == $statsData['min_likes'] &&
            $tag->lastTagStats->max_likes == $statsData['max_likes'] &&
            $tag->lastTagStats->min_comments == $statsData['min_comments'] &&
            $tag->lastTagStats->max_comments == $statsData['max_comments']
        ) {
            return null;
        }

        $tagStats = new TagStats();
        $tagStats->attributes = $statsData;
        $tag->link('tagStats', $tagStats);

        return $tagStats;
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

    protected function fetchContent($url, Tag $tag): array
    {
        $proxy = $this->getProxy($tag);
        $client = Client::factory($proxy);
        $res = $client->get($url);

        return Json::decode($res->getBody()->getContents());
    }
}