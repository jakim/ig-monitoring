<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.05.2018
 */

namespace app\modules\api\v1\models;


use yii\behaviors\AttributeTypecastBehavior;
use yii\helpers\ArrayHelper;

class Account extends \app\models\Account
{
    public $as_followed_by;
    public $as_follows;
    public $as_media;
    public $as_er;
    public $as_created_at;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'type' => [
                'class' => AttributeTypecastBehavior::class,
                'typecastAfterValidate' => false,
                'typecastAfterSave' => true,
                'typecastAfterFind' => true,
            ],
        ]);
    }

    public function fields()
    {
        return [
            'id',
            'uid',
            'username',
            'monitoring',
            'disabled',
            'name',
            'profile_pic_url',
            'full_name',
            'biography',
            'external_url',
            'instagram_id',
            'notes',
            'updated_at',
            'created_at',

//            'followed_by' => 'as_followed_by',
//            'follows' => 'as_follows',
//            'media' => 'as_media',
//            'er' => 'as_er',
        ];
    }
}