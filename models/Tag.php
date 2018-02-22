<?php

namespace app\models;

use app\dictionaries\ProxyType;
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
 * @property int $main_tag_id
 * @property string $updated_at
 * @property string $created_at
 * @property int $monitoring
 * @property int $proxy_id
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
 * @property Tag $mainTag
 * @property Tag[] $tags
 * @property TagStats[] $tagStats
 */
class Tag extends \yii\db\ActiveRecord
{
    public function proxyType()
    {
        return ProxyType::TAG;
    }

    public $occurs;

    public function getNamePrefixed()
    {
        return "#{$this->name}";
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'time' => TimestampBehavior::class,
            'slug' => SluggableBehavior::class,
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
            [['main_tag_id', 'monitoring', 'proxy_id', 'occurs'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['proxy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Proxy::class, 'targetAttribute' => ['proxy_id' => 'id']],
            [['main_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['main_tag_id' => 'id']],
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
            'main_tag_id' => 'Main Tag ID',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'monitoring' => 'Monitoring',
            'proxy_id' => 'Proxy ID',
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
        if ($this->proxy_id) {
            return $this->hasOne(Proxy::class, ['id' => 'proxy_id']);
        }

        return Proxy::find()
            ->andWhere(['type' => ProxyType::TAG])
            ->orderBy(new Expression('RAND()'))
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainTag()
    {
        return $this->hasOne(Tag::class, ['id' => 'main_tag_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['main_tag_id' => 'id']);
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
            ->orderBy('tag_stats.id DESC');
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
