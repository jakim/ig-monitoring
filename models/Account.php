<?php

namespace app\models;

use app\components\UidAttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string $uid
 * @property string $name
 * @property string $username
 * @property string $profile_pic_url
 * @property string $full_name
 * @property string $biography
 * @property string $external_url
 * @property string $instagram_id
 * @property string $updated_at
 * @property string $created_at
 * @property bool $monitoring
 * @property bool $is_valid [tinyint(1)]
 * @property int $invalidation_type_id [int(11)]
 * @property int $invalidation_count [int(11)]
 * @property string $update_stats_after [datetime]
 * @property int $followed_by [int(11)]
 * @property int $follows [int(11)]
 * @property string $er [decimal(19,4)]
 * @property string $avg_likes [decimal(19,4)]
 * @property string $avg_comments [decimal(19,4)]
 * @property string $stats_updated_at [datetime]
 * @property bool $is_verified [tinyint(1)]
 * @property bool $is_business [tinyint(1)]
 * @property string $business_category [varchar(255)]
 * @property string $last_post_taken_at [datetime]
 *
 * @property string $usernamePrefixed
 * @property string $displayName
 *
 * @property AccountStats $lastAccountStats
 *
 * @property AccountInvalidationType $invalidationType
 * @property AccountCategory[] $accountCategories
 * @property Category[] $categories
 * @property AccountNote[] $accountNotes
 * @property AccountStats[] $accountStats
 * @property Media[] $media
 * @property MediaAccount[] $mediaAccounts
 * @property \app\models\Account[] $accounts
 */
class Account extends ActiveRecord
{
    public $occurs;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'time' => TimestampBehavior::class,
            'uid' => UidAttributeBehavior::class,
        ]);
    }

    public function getDisplayName()
    {
        return $this->name ?: $this->getUsernamePrefixed();
    }

    public function getUsernamePrefixed()
    {
        return "@{$this->username}";
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['updated_at', 'created_at', 'stats_updated_at', 'last_post_taken_at'], 'safe'],
            [['occurs', 'followed_by', 'follows', 'media'], 'integer'],
            [['er', 'avg_likes', 'avg_comments'], 'number'],
            [['name', 'username', 'profile_pic_url', 'full_name', 'biography', 'external_url', 'instagram_id', '!uid', 'business_category'], 'string', 'max' => 255],
            [['monitoring', 'is_valid', 'is_valid', 'is_business'], 'boolean'],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'username' => 'Username',
            'profile_pic_url' => 'Profile Pic Url',
            'full_name' => 'Full Name',
            'biography' => 'Biography',
            'external_url' => 'External Url',
            'instagram_id' => 'Instagram ID',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'monitoring' => 'Monitoring',
            'er' => 'Engagement',
        ];
    }

    public function attributeHints()
    {
        return [
            'name' => 'The name displayed in the lists, if empty, the \'username\' will be used.',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvalidationType()
    {
        return $this->hasOne(AccountInvalidationType::class, ['id' => 'invalidation_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountCategories()
    {
        return $this->hasMany(AccountCategory::class, ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])->viaTable('account_category', ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountNotes()
    {
        return $this->hasMany(AccountNote::class, ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountStats()
    {
        return $this->hasMany(AccountStats::class, ['account_id' => 'id']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasMany(Media::class, ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaMediaAccounts()
    {
        return $this->hasMany(MediaAccount::class, ['media_id' => 'id'])
            ->via('media');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::class, ['id' => 'account_id'])
            ->via('mediaMediaAccounts');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaAccounts()
    {
        return $this->hasMany(MediaAccount::class, ['account_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return AccountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AccountQuery(get_called_class());
    }
}
