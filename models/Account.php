<?php

namespace app\models;

use app\dictionaries\ProxyType;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string $username
 * @property string $profile_pic_url
 * @property string $full_name
 * @property string $biography
 * @property string $external_url
 * @property string $instagram_id
 * @property string $updated_at
 * @property string $created_at
 * @property int $monitoring
 * @property int $proxy_id
 * @property string $notes
 *
 * @property string $usernamePrefixed
 *
 * @property AccountStats $lastAccountStats
 * @property AccountStats $beforeLastAccountStats
 * @property AccountStats $beforeMonthAccountStats
 * @property AccountStats[] $monthAccountStats
 *
 * @property Proxy $proxy
 * @property AccountStats[] $accountStats
 * @property AccountTag[] $accountTags
 * @property Tag[] $tags
 * @property Media[] $media
 * @property MediaAccount[] $mediaAccounts
 */
class Account extends \yii\db\ActiveRecord
{
    public $occurs;

    /**
     * @var \app\models\AccountStats
     */
    protected $lastAccountStats;

    /**
     * @var \app\models\AccountStats
     */
    protected $beforeLastAccountStats;

    /**
     * @var \app\models\AccountStats
     */
    protected $beforeMonthAccountStats;

    /**
     * @var \app\models\AccountStats[]
     */
    private $statsCache;

    public function monthlyChange($attribute)
    {
        $last = $this->getLastAccountStats();
        $beforeMonth = $this->getBeforeMonthAccountStats();
        if (!$beforeMonth) {
            return 0;
        }

        return $last->$attribute - $beforeMonth->$attribute;
    }

    public function lastChange($attribute)
    {
        $last = $this->getLastAccountStats();
        $beforeLast = $this->getBeforeLastAccountStats();
        if (!$beforeLast) {
            return 0;
        }

        return $last->$attribute - $beforeLast->$attribute;
    }

    public function getBeforeMonthAccountStats()
    {
        if ($this->beforeMonthAccountStats) {
            return $this->beforeMonthAccountStats;
        }
        if (!$this->statsCache) {
            $this->statsCache = $this->getMonthAccountStats();
        }
        if (count($this->statsCache) >= 2) {
            return $this->beforeMonthAccountStats = end($this->statsCache);
        }

        return null;
    }

    public function getBeforeLastAccountStats()
    {
        if ($this->beforeLastAccountStats) {
            return $this->beforeLastAccountStats;
        }
        if (!$this->statsCache) {
            $this->statsCache = $this->getMonthAccountStats();
        }
        if (count($this->statsCache) >= 2) {
            return $this->beforeLastAccountStats = $this->statsCache['1'];
        }

        return null;
    }

    public function getLastAccountStats()
    {
        if ($this->lastAccountStats) {
            return $this->lastAccountStats;
        }
        if (!$this->statsCache) {
            $this->statsCache = $this->getMonthAccountStats();
        }
        if (count($this->statsCache) >= 1) {
            return $this->lastAccountStats = $this->statsCache['0'];
        }

        return null;
    }

    /**
     * @return \app\models\AccountStats[]
     */
    public function getMonthAccountStats()
    {
        return $this->getAccountStats()
            ->andWhere(new Expression('account_stats.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)'))
            ->orderBy('account_stats.id DESC')
            ->all();
    }

    public function proxyType()
    {
        return ProxyType::ACCOUNT;
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'time' => TimestampBehavior::class,
        ]);
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
            [['updated_at', 'created_at'], 'safe'],
            [['monitoring', 'proxy_id', 'occurs'], 'integer'],
            [['username', 'profile_pic_url', 'full_name', 'biography', 'external_url', 'instagram_id', 'notes'], 'string', 'max' => 255],
            [['username'], 'unique'],
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
            'username' => 'Username',
            'profile_pic_url' => 'Profile Pic Url',
            'full_name' => 'Full Name',
            'biography' => 'Biography',
            'external_url' => 'External Url',
            'instagram_id' => 'Instagram ID',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'monitoring' => 'Monitoring',
            'proxy_id' => 'Proxy ID',
            'notes' => 'Notes',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProxy()
    {
        if ($this->proxy_id) {
            return $this->hasOne(Proxy::class, ['id' => 'proxy_id']);
        }

        return Proxy::find()
            ->andWhere(['type' => ProxyType::ACCOUNT])
            ->orderBy(new Expression('RAND()'));
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
    public function getAccountTags()
    {
        return $this->hasMany(AccountTag::class, ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('accountTags');
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
