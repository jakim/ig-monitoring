<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "media_tag".
 *
 * @property int $media_id
 * @property int $tag_id
 * @property string $created_at
 *
 * @property Media $media
 * @property Tag $tag
 */
class MediaTag extends ActiveRecord
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
        return 'media_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['media_id', 'tag_id'], 'required'],
            [['media_id', 'tag_id'], 'integer'],
            [['created_at'], 'safe'],
            [['media_id', 'tag_id'], 'unique', 'targetAttribute' => ['media_id', 'tag_id']],
            [['media_id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::class, 'targetAttribute' => ['media_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'media_id' => 'Media ID',
            'tag_id' => 'Tag ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id']);
    }

    /**
     * @inheritdoc
     * @return MediaTagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MediaTagQuery(get_called_class());
    }
}
