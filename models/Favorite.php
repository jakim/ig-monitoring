<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "favorite".
 *
 * @property int $id
 * @property string $label
 * @property string $url
 * @property string $created_at
 * @property int $user_id [int(11)]
 */
class Favorite extends ActiveRecord
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'time' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'favorite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label', 'url'], 'required'],
            [['created_at'], 'safe'],
            [['label', 'url'], 'string', 'max' => 255],
            [['user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => 'Label',
            'url' => 'Url',
            'created_at' => 'Created At',
        ];
    }
}
