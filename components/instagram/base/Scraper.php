<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 21.06.2018
 */

namespace app\components\instagram\base;


use app\components\instagram\models\Post;
use yii\base\Component;

abstract class Scraper extends Component
{
    /**
     * @var \GuzzleHttp\Client
     */
    public $httpClient;

    public function __construct($httpClient, array $config = [])
    {
        $this->httpClient = $httpClient;
        parent::__construct($config);
    }

    /**
     * @param $posts
     * @return array
     */
    protected function preparePosts($posts): array
    {
        $arr = [];
        foreach ($posts as $post) {
            $model = new Post();
            $model->id = $post->id;
            $model->shortcode = $post->shortcode;
            $model->url = $post->url;
            $model->isVideo = $post->isVideo;
            $model->caption = $post->caption;
            $model->takenAt = $post->takenAt;
            $model->likes = $post->likes;
            $model->comments = $post->comments;
            $arr[] = $model;
        }

        return $arr;
    }
}