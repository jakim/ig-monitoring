<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[MediaTag]].
 *
 * @see MediaTag
 */
class MediaTagQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MediaTag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MediaTag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
