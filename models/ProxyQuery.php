<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Proxy]].
 *
 * @see Proxy
 */
class ProxyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Proxy[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Proxy|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
