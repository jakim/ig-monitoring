<?php

namespace app\models;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Account]].
 *
 * @see Account
 */
class AccountQuery extends ActiveQuery
{
    public function monitoring()
    {
        return $this->andWhere(['account.monitoring' => 1]);
    }
}
