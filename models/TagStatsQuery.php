<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TagStats]].
 *
 * @see TagStats
 */
class TagStatsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TagStats[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TagStats|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
