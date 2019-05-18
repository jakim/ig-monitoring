<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tag_stats".
 *
 * @property int $id
 * @property int $tag_id
 * @property int $media
 * @property int $likes
 * @property int $comments
 * @property int $min_likes
 * @property int $max_likes
 * @property int $min_comments
 * @property int $max_comments
 * @property string $created_at
 *
 * @property Tag $tag
 */
class TagStats extends ActiveRecord
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
        return 'tag_stats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'media', 'likes', 'comments', 'min_likes', 'max_likes', 'min_comments', 'max_comments'], 'integer'],
            [['created_at'], 'safe'],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_id' => 'Tag ID',
            'media' => 'Media',
            'likes' => 'Likes',
            'comments' => 'Comments',
            'min_likes' => 'Min Likes',
            'max_likes' => 'Max Likes',
            'min_comments' => 'Min Comments',
            'max_comments' => 'Max Comments',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id']);
    }

    /**
     * @inheritdoc
     * @return TagStatsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TagStatsQuery(get_called_class());
    }
}
