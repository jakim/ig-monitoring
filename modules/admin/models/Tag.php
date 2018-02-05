<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 23.01.2018
 */

namespace app\modules\admin\models;


use app\components\ArrayHelper;

class Tag extends \app\models\Tag
{
    public $ts_media;
    public $ts_likes;
    public $ts_comments;
    public $ts_min_likes;
    public $ts_max_likes;
    public $ts_min_comments;
    public $ts_max_comments;

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'ts_media' => 'Media',
            'ts_likes' => 'Likes',
            'ts_comments' => 'Comments',
            'ts_min_likes' => 'Min Likes',
            'ts_max_likes' => 'Max Likes',
            'ts_min_comments' => 'Min Comments',
            'ts_max_comments' => 'Max Comments',
        ]);
    }
}