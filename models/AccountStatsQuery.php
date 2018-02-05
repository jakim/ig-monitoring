<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[AccountStats]].
 *
 * @see AccountStats
 */
class AccountStatsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return AccountStats[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AccountStats|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
