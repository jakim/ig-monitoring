<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "media_stats".
 *
 * @property int $id
 * @property int $media_id
 * @property int $likes
 * @property int $comments
 * @property int $account_followed_by
 * @property int $account_follows
 * @property string $created_at
 *
 * @property Media $media
 */
class MediaStats extends \yii\db\ActiveRecord
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
        return 'media_stats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['media_id', 'likes', 'comments', 'account_followed_by', 'account_follows'], 'integer'],
            [['created_at'], 'safe'],
            [['media_id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::class, 'targetAttribute' => ['media_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'media_id' => 'Media ID',
            'likes' => 'Likes',
            'comments' => 'Comments',
            'account_followed_by' => 'Account Followed By',
            'account_follows' => 'Account Follows',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasOne(Media::class, ['id' => 'media_id']);
    }

    /**
     * @inheritdoc
     * @return MediaStatsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MediaStatsQuery(get_called_class());
    }
}
