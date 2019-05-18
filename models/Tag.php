<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $updated_at
 * @property string $created_at
 * @property int $monitoring
 * @property int $proxy_id
 * @property bool $is_valid
 * @property int $invalidation_type_id
 * @property int $invalidation_count
 * @property string $update_stats_after
 * @property bool $disabled
 * @property int $likes [int(11)]
 * @property int $min_likes [int(11)]
 * @property int $max_likes [int(11)]
 * @property int $comments [int(11)]
 * @property int $min_comments [int(11)]
 * @property int $max_comments [int(11)]
 * @property string $stats_updated_at [datetime]
 *
 * @property string $namePrefixed
 *
 * @property TagStats $lastTagStats
 *
 * @property AccountTag[] $accountTags
 * @property Account[] $accounts
 * @property MediaTag[] $mediaTags
 * @property Media[] $media
 * @property Proxy $proxy
 * @property TagInvalidationType $invalidationType
 * @property TagStats[] $tagStats
 */
class Tag extends \yii\db\ActiveRecord
{
    const SCENARIO_UPDATE = 'update';

    public $occurs;
    public $ts_avg_likes;

    public function getNamePrefixed()
    {
        return "#{$this->name}";
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'time' => TimestampBehavior::class,
            'slug' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'immutable' => true,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['monitoring', 'proxy_id', 'occurs', 'invalidation_type_id', 'invalidation_count', 'media', 'likes', 'min_likes', 'max_likes', 'comments', 'min_comments', 'max_comments'], 'integer'],
            [['is_valid', 'disabled'], 'boolean'],
            [['updated_at', 'created_at', 'update_stats_after', 'stats_updated_at'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['invalidation_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TagInvalidationType::class, 'targetAttribute' => ['invalidation_type_id' => 'id']],
            [['proxy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Proxy::class, 'targetAttribute' => ['proxy_id' => 'id']],
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
            'slug' => 'Slug',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'monitoring' => 'Monitoring',
            'proxy_id' => 'Proxy ID',
            'is_valid' => 'Is Valid',
            'invalidation_type_id' => 'Invalidation Type ID',
            'invalidation_count' => 'Invalidation Count',
            'update_stats_after' => 'Update Stats After',
            'disabled' => 'Disabled',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountTags()
    {
        return $this->hasMany(AccountTag::class, ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::class, ['id' => 'account_id'])->viaTable('account_tag', ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaTags()
    {
        return $this->hasMany(MediaTag::class, ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasMany(Media::class, ['id' => 'media_id'])->viaTable('media_tag', ['tag_id' => 'id']);
    }

    public function getProxy()
    {
        return $this->hasOne(Proxy::class, ['id' => 'proxy_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvalidationType()
    {
        return $this->hasOne(TagInvalidationType::className(), ['id' => 'invalidation_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagStats()
    {
        return $this->hasMany(TagStats::class, ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastTagStats()
    {
        return $this->hasOne(TagStats::class, ['tag_id' => 'id'])
            ->orderBy('tag_stats.id DESC')
            ->limit(1);
    }

    /**
     * @inheritdoc
     * @return TagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TagQuery(get_called_class());
    }
}
