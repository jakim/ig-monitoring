<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 21.06.2018
 */

namespace app\components\instagram;


use app\components\instagram\base\Scraper;
use app\components\instagram\contracts\TagScraperInterface;
use app\components\instagram\models\Tag;
use Jakim\Query\TagQuery;

class TagScraper extends Scraper implements TagScraperInterface
{

    public function fetchOne(string $name): ?Tag
    {
        $query = new TagQuery($this->httpClient);
        $tag = $query->findOne($name);

        $model = new Tag();
        $model->name = $tag->name;
        $model->media = $tag->media;

        $model->likes = $tag->likes;
        $model->minLikes = $tag->minLikes;
        $model->maxLikes = $tag->maxLikes;
        $model->comments = $tag->comments;
        $model->minComments = $tag->minComments;
        $model->maxComments = $tag->maxComments;

        return $model;
    }

    /**
     * @param string $name
     * @return \app\components\instagram\models\Post[]|array
     */
    public function fetchTopPosts(string $name): array
    {
        $query = new TagQuery($this->httpClient);
        $posts = $query->findTopPosts($name);
        $posts = $this->preparePosts($posts);

        return $posts;
    }
}