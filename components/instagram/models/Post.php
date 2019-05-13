<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.06.2018
 */

namespace app\components\instagram\models;


class Post
{
    public $id;
    public $shortcode;
    public $url;
    public $isVideo;
    public $caption;
    public $takenAt;
    public $likes;
    public $comments;

    // related
    /**
     * @var \app\components\instagram\models\Account
     */
    public $account;

}