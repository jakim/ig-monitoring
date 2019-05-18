<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "media".
 *
 * @property int $id
 * @property int $account_id
 * @property string $shortcode
 * @property int $is_video
 * @property string $caption
 * @property string $instagram_id
 * @property string $taken_at
 * @property string $updated_at
 * @property string $created_at
 * @property int $likes
 * @property int $comments
 *
 * @property Account $account
 * @property MediaAccount[] $mediaAccounts
 * @property Account[] $accounts
 * @property MediaTag[] $mediaTags
 * @property Tag[] $tags
 */
class Media extends ActiveRecord
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'time' => TimestampBehavior::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'likes', 'comments'], 'integer'],
            [['is_video'], 'boolean'],
            [['shortcode'], 'required'],
            [['caption'], 'string'],
            [['taken_at', 'updated_at', 'created_at'], 'safe'],
            [['shortcode', 'instagram_id'], 'string', 'max' => 255],
            [['shortcode'], 'unique'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
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
            'shortcode' => 'Shortcode',
            'is_video' => 'Is Video',
            'caption' => 'Caption',
            'instagram_id' => 'Instagram ID',
            'taken_at' => 'Taken At',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'likes' => 'Likes',
            'comments' => 'Comments',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaAccounts()
    {
        return $this->hasMany(MediaAccount::class, ['media_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::class, ['id' => 'account_id'])->viaTable('media_account', ['media_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaTags()
    {
        return $this->hasMany(MediaTag::class, ['media_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->viaTable('media_tag', ['media_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return MediaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MediaQuery(get_called_class());
    }
}
