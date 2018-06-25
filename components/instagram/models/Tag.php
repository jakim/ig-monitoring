<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.06.2018
 */

namespace app\components\instagram\models;


class Tag
{
    public $name;
    public $media;

    public $likes;
    public $minLikes;
    public $maxLikes;
    public $comments;
    public $minComments;
    public $maxComments;
}