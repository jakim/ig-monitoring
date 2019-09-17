<?php

namespace app\models;

use app\components\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
 * @property string $reservation_uid
 * @property int $rests [int(11)]
 * @property string $rest_until [datetime]
 *
 * @property string $curlString
 */
class Proxy extends ActiveRecord
{

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
            [['port', 'rests'], 'integer'],
            [['updated_at', 'created_at', 'rest_until'], 'safe'],
            [['ip', 'username', 'password', 'reservation_uid'], 'string', 'max' => 255],
            [['active'], 'boolean'],
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
        ];
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
