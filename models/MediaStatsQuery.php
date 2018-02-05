<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[MediaStats]].
 *
 * @see MediaStats
 */
class MediaStatsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MediaStats[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MediaStats|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
