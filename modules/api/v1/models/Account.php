<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.05.2018
 */

namespace app\modules\api\v1\models;


use yii\behaviors\AttributeTypecastBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Account extends \app\models\Account
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
            'id',
            'uid',
            'username',
            'monitoring',
            'disabled',
            'name',
            'profile_pic_url' => function() {
                return Url::to($this->profile_pic_url, true);
            },
            'full_name',
            'biography',
            'external_url',
            'instagram_id',
            'updated_at',
            'created_at',
        ];
    }

    public function extraFields()
    {
        return [
            'lastAccountStats',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastAccountStats()
    {
        return $this->hasOne(AccountStats::class, ['account_id' => 'id'])
            ->orderBy('account_stats.id DESC')
            ->limit(1);
    }
}