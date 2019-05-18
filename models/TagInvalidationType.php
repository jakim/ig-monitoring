<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "tag_invalidation_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property Tag[] $tags
 */
class TagInvalidationType extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag_invalidation_type';
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
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['invalidation_type_id' => 'id']);
    }
}
