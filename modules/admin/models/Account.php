<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 23.01.2018
 */

namespace app\modules\admin\models;


use app\components\ArrayHelper;

class Account extends \app\models\Account
{
    public $as_followed_by;
    public $as_follows;
    public $as_media;
    public $as_er;

    public $s_tags;

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'as_followed_by' => 'Followed By',
            'as_follows' => 'Follows',
            'as_media' => 'Media',
            'as_er' => 'Er',
            's_tags' => 'Tags',
        ]);
    }
}