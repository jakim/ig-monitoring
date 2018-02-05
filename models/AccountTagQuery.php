<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[AccountTag]].
 *
 * @see AccountTag
 */
class AccountTagQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return AccountTag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AccountTag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
