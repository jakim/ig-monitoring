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
 * @property int $main_tag_id
 * @property string $updated_at
 * @property string $created_at
 * @property int $monitoring
 * @property int $proxy_id
 * @property int $proxy_tag_id
 *
 * @property string $namePrefixed
 *
 * @property TagStats $lastTagStats
 * @property TagStats $beforeLastTagStats
 * @property TagStats $beforeMonthTagStats
 * @property TagStats[] $monthTagStats
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
    public $occurs;

    /**
     * @var \app\models\TagStats
     */
    protected $lastTagStats;

    /**
     * @var \app\models\TagStats
     */
    protected $beforeLastTagStats;

    /**
     * @var \app\models\TagStats
     */
    protected $beforeMonthTagStats;

    /**
     * @var \app\models\TagStats[]
     */
    private $statsCache;

    public function resetStatsCache()
    {
        $this->statsCache = null;
        $this->lastTagStats = null;
        $this->beforeLastTagStats = null;
        $this->beforeMonthTagStats = null;
    }

    public function monthlyChange($attribute)
    {
        $last = $this->getLastTagStats();
        $beforeMonth = $this->getBeforeMonthTagStats();
        if (!$beforeMonth) {
            return 0;
        }

        return $last->$attribute - $beforeMonth->$attribute;
    }

    public function lastChange($attribute)
    {
        $last = $this->getLastTagStats();
        $beforeLast = $this->getBeforeLastTagStats();
        if (!$beforeLast) {
            return 0;
        }

        return $last->$attribute - $beforeLast->$attribute;
    }

    public function getBeforeMonthTagStats()
    {
        if ($this->beforeMonthTagStats) {
            return $this->beforeMonthTagStats;
        }
        if (!$this->statsCache) {
            $this->statsCache = $this->getMonthTagStats();
        }
        if (count($this->statsCache) >= 2) {
            return $this->beforeMonthTagStats = end($this->statsCache);
        }

        return null;
    }

    public function getBeforeLastTagStats()
    {
        if ($this->beforeLastTagStats) {
            return $this->beforeLastTagStats;
        }
        if (!$this->statsCache) {
            $this->statsCache = $this->getMonthTagStats();
        }
        if (count($this->statsCache) >= 2) {
            return $this->beforeLastTagStats = $this->statsCache['1'];
        }

        return null;
    }

    public function getLastTagStats()
    {
        if ($this->lastTagStats) {
            return $this->lastTagStats;
        }
        if (!$this->statsCache) {
            $this->statsCache = $this->getMonthTagStats();
        }
        if (count($this->statsCache) >= 1) {
            return $this->lastTagStats = $this->statsCache['0'];
        }

        return null;
    }

    /**
     * @return \app\models\TagStats[]
     */
    public function getMonthTagStats()
    {
        return $this->getTagStats()
            ->andWhere(new Expression('tag_stats.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)'))
            ->orderBy('tag_stats.id DESC')
            ->all();
    }

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
            [['proxy_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['proxy_tag_id' => 'id']],
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
            'proxy_tag_id' => 'Proxy Tag ID',
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

        if ($this->proxy_tag_id) {
            return Proxy::find()
                ->innerJoinWith(['proxyTag'])
                ->andWhere(['proxy_tag.tag_id' => $this->proxy_tag_id])
                ->orderBy(new Expression('RAND()'))
                ->one();
        }

        return Proxy::find()
            ->defaultForTags()
            ->orderBy(new Expression('RAND()'))
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProxyTag()
    {
        return $this->hasOne(Tag::class, ['id' => 'proxy_tag_id']);
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
     * @inheritdoc
     * @return TagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TagQuery(get_called_class());
    }
}
