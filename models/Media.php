<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
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
 * @property int $monitoring
 * @property int $proxy_id
 *
 * @property MediaStats $lastMediaStats
 *
 * @property Account $account
 * @property Proxy $proxy
 * @property MediaAccount[] $mediaAccounts
 * @property Account[] $accounts
 * @property MediaStats[] $mediaStats
 * @property MediaTag[] $mediaTags
 * @property Tag[] $tags
 */
class Media extends \yii\db\ActiveRecord
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
            [['account_id', 'proxy_id'], 'integer'],
            [['is_video', 'monitoring'], 'boolean'],
            [['shortcode'], 'required'],
            [['caption'], 'string'],
            [['taken_at', 'updated_at', 'created_at'], 'safe'],
            [['shortcode', 'instagram_id'], 'string', 'max' => 255],
            [['shortcode'], 'unique'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['proxy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Proxy::className(), 'targetAttribute' => ['proxy_id' => 'id']],
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
            'monitoring' => 'Monitoring',
            'proxy_id' => 'Proxy ID',
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
    public function getProxy()
    {
        return $this->hasOne(Proxy::className(), ['id' => 'proxy_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaStats()
    {
        return $this->hasMany(MediaStats::className(), ['media_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaAccounts()
    {
        return $this->hasMany(MediaAccount::className(), ['media_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::className(), ['id' => 'account_id'])->viaTable('media_account', ['media_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastMediaStats()
    {
        return $this->hasOne(MediaStats::className(), ['media_id' => 'id'])
            ->orderBy('media_stats.id DESC')
            ->limit(1);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaTags()
    {
        return $this->hasMany(MediaTag::className(), ['media_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable('media_tag', ['media_id' => 'id']);
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
