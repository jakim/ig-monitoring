<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 21.06.2018
 */

namespace app\components\instagram\base;


use app\components\instagram\models\Account;
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
     * @return Post[]|array
     */
    protected function preparePosts($posts): array
    {
        $arr = [];
        foreach ($posts as $post) {
            $arr[] = $this->preparePost($post);
        }

        return $arr;
    }

    /**
     * @param \Jakim\Model\Post $post
     * @return \app\components\instagram\models\Post
     */
    protected function preparePost(\Jakim\Model\Post $post): Post
    {
        $model = new Post();
        $model->id = $post->id;
        $model->shortcode = $post->shortcode;
        $model->url = $post->url;
        $model->isVideo = $post->isVideo;
        $model->caption = $post->caption;
        $model->takenAt = $post->takenAt;
        $model->likes = $post->likes;
        $model->comments = $post->comments;

        if ($post->account) {
            $account = new Account();
            $account->id = $post->account->id;
            $account->profilePicUrl = $post->account->profilePicUrl;
            $account->username = $post->account->username;
            $account->fullName = $post->account->fullName;
            $account->isPrivate = $post->account->isPrivate;
            $model->account = $account;
        }

        return $model;
    }
}