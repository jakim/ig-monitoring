<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[MediaAccount]].
 *
 * @see MediaAccount
 */
class MediaAccountQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MediaAccount[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MediaAccount|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
