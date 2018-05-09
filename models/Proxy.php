<?php

namespace app\models;

use app\components\ArrayHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "proxy".
 *
 * @property int $id
 * @property string $ip
 * @property int $port
 * @property string $username
 * @property string $password
 * @property int $active
 * @property string $updated_at
 * @property string $created_at
 * @property bool $default_for_accounts
 * @property bool $default_for_tags
 * @property string $reservation_uid
 *
 * @property string $curlString
 *
 * @property ProxyTag[] $proxyTags
 * @property Tag[] $tags
 */
class Proxy extends \yii\db\ActiveRecord
{

    public static function usedTags()
    {
        return Tag::find()
            ->innerJoin('proxy_tag', 'tag.id=proxy_tag.tag_id')
            ->orderBy('name ASC')
            ->all();
    }

    public function getCurlString()
    {
        $url = "{$this->ip}:{$this->port}";
        if ($this->username && $this->password) {
            return "{$this->username}:{$this->password}@{$url}";
        }

        return $url;
    }

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
        return 'proxy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip', 'port'], 'required'],
            [['ip'], 'ip'],
            [['port'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['ip', 'username', 'password', 'reservation_uid'], 'string', 'max' => 255],
            [['active', 'default_for_accounts', 'default_for_tags'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'Ip',
            'port' => 'Port',
            'username' => 'Username',
            'password' => 'Password',
            'active' => 'Active',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'default_for_accounts' => 'Default For Accounts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProxyTags()
    {
        return $this->hasMany(ProxyTag::class, ['proxy_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->viaTable('proxy_tag', ['proxy_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return ProxyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProxyQuery(get_called_class());
    }
}
