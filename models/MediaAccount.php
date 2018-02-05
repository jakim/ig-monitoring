<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "media_account".
 *
 * @property int $media_id
 * @property int $account_id
 * @property string $created_at
 *
 * @property Account $account
 * @property Media $media
 */
class MediaAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['media_id', 'account_id'], 'required'],
            [['media_id', 'account_id'], 'integer'],
            [['created_at'], 'safe'],
            [['media_id', 'account_id'], 'unique', 'targetAttribute' => ['media_id', 'account_id']],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['media_id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::className(), 'targetAttribute' => ['media_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'media_id' => 'Media ID',
            'account_id' => 'Account ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasOne(Media::className(), ['id' => 'media_id']);
    }

    /**
     * @inheritdoc
     * @return MediaAccountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MediaAccountQuery(get_called_class());
    }
}
