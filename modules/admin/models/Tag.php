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
    public $ts_created_at;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = [
            'is_valid',
        ];

        return $scenarios;
    }

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
            'ts_created_at' => 'Created At',
            'is_valid' => 'Is Valid - an exclamation triangle in the list of tags, is set automatically if the tag is not reachable. Check this option if you are sure that this tag is valid and want to try to refresh stats again.',
        ]);
    }
}