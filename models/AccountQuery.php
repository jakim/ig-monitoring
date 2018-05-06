<?php

namespace app\models;
use app\modules\api\v1\models\Account;

/**
 * This is the ActiveQuery class for [[Account]].
 *
 * @see Account
 */
class AccountQuery extends \yii\db\ActiveQuery
{
    public function monitoring()
    {
        return $this->andWhere(['account.monitoring' => 1])
            ->andWhere(['disabled' => 0]);
    }
}
