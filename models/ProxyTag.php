<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proxy_tag".
 *
 * @property int $proxy_id
 * @property int $tag_id
 *
 * @property Proxy $proxy
 * @property Tag $tag
 */
class ProxyTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proxy_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['proxy_id', 'tag_id'], 'required'],
            [['proxy_id', 'tag_id'], 'integer'],
            [['proxy_id', 'tag_id'], 'unique', 'targetAttribute' => ['proxy_id', 'tag_id']],
            [['proxy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Proxy::class, 'targetAttribute' => ['proxy_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'proxy_id' => 'Proxy ID',
            'tag_id' => 'Tag ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProxy()
    {
        return $this->hasOne(Proxy::class, ['id' => 'proxy_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id']);
    }
}
