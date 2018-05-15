<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.05.2018
 */

namespace app\modules\api\v1\models;


use yii\behaviors\AttributeTypecastBehavior;
use yii\helpers\ArrayHelper;

class AccountStats extends \app\models\AccountStats
{
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
            'followed_by',
            'follows',
            'media',
            'er',
            'created_at',
        ];
    }
}