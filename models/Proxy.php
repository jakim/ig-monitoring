<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "proxy".
 *
 * @property int $id
 * @property string $ip
 * @property int $port
 * @property string $username
 * @property string $password
 * @property int $active
 * @property string $type
 * @property string $updated_at
 * @property string $created_at
 *
 * @property string $curlString
 *
 * @property Account[] $accounts
 * @property Media[] $media
 * @property Tag[] $tags
 */
class Proxy extends \yii\db\ActiveRecord
{

    public function getCurlString()
    {
        return "{$this->username}:{$this->password}@{$this->ip}:{$this->port}";
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
            [['port', 'active'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['ip', 'username', 'password', 'type'], 'string', 'max' => 255],
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
            'type' => 'Type',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::class, ['proxy_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasMany(Media::class, ['proxy_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['proxy_id' => 'id']);
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
