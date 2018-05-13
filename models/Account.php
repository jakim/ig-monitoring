<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
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
 * @property int $proxy_id
 * @property int $proxy_tag_id
 * @property string $notes
 * @property bool $disabled
 *
 * @property string $usernamePrefixed
 * @property string $displayName
 *
 * @property AccountStats $lastAccountStats
 *
 * @property Proxy $proxy
 * @property Tag $proxyTag
 * @property AccountStats[] $accountStats
 * @property AccountTag[] $accountTags
 * @property Tag[] $tags
 * @property Media[] $media
 * @property MediaAccount[] $mediaAccounts
 */
class Account extends \yii\db\ActiveRecord
{
    public $occurs;

    public static function usedTags()
    {
        return Tag::find()
            ->innerJoin('account_tag', 'tag.id=account_tag.tag_id')
            ->orderBy('tag.slug ASC')
            ->all();
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'time' => TimestampBehavior::class,
            'uid' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['uid'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['uid'],
                ],
                'preserveNonEmptyValues' => true,
                'value' => function () {
                    do {
                        $uid = Yii::$app->security->generateRandomString(64);
                        $uidExist = static::find()
                            ->andWhere(['account.uid' => $uid])
                            ->exists();
                    } while ($uidExist);

                    return $uid;
                },
            ],
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
            [['updated_at', 'created_at'], 'safe'],
            [['proxy_id', 'proxy_tag_id', 'occurs'], 'integer'],
            [['name', 'username', 'profile_pic_url', 'full_name', 'biography', 'external_url', 'instagram_id', 'notes', 'uid'], 'string', 'max' => 255],
            [['monitoring','disabled'], 'boolean'],
            [['username'], 'unique'],
            [['proxy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Proxy::class, 'targetAttribute' => ['proxy_id' => 'id']],
            [['proxy_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['proxy_tag_id' => 'id']],
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
            'proxy_id' => 'Proxy ID',
            'proxy_tag_id' => 'Proxy Tag ID',
            'notes' => 'Notes',
        ];
    }

    public function getProxy()
    {
        if ($this->proxy_id) {
            return $this->hasOne(Proxy::class, ['id' => 'proxy_id']);
        }

        if ($this->proxy_tag_id) {
            return Proxy::find()
                ->innerJoinWith('proxyTags')
                ->andWhere(['proxy_tag.tag_id' => $this->proxy_tag_id])
                ->orderBy(new Expression('RAND()'));
        }

        return Proxy::find()
            ->defaultForAccounts()
            ->orderBy(new Expression('RAND()'));
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
