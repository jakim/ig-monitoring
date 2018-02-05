<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "account_stats".
 *
 * @property int $id
 * @property int $account_id
 * @property int $followed_by
 * @property int $follows
 * @property int $media
 * @property string $er [decimal(4,2)]
 * @property string $created_at
 *
 * @property Account $account
 */
class AccountStats extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'time' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_stats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id'], 'required'],
            [['account_id', 'followed_by', 'follows', 'media'], 'integer'],
            [['er'], 'number'],
            [['created_at'], 'safe'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'followed_by' => 'Followed By',
            'follows' => 'Follows',
            'media' => 'Media',
            'er' => 'Er',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * @inheritdoc
     * @return AccountStatsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AccountStatsQuery(get_called_class());
    }
}
