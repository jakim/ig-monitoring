<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Account]].
 *
 * @see Account
 */
class AccountQuery extends \yii\db\ActiveQuery
{
    public function monitoring()
    {
        return $this->andWhere(['account.monitoring' => 1]);
    }
}
