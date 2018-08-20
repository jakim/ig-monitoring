<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "account_invalidation_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property Account[] $accounts
 */
class AccountInvalidationType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account_invalidation_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::className(), ['invalidation_type_id' => 'id']);
    }
}
