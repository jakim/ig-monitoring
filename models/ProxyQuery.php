<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Proxy]].
 *
 * @see Proxy
 */
class ProxyQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['active' => 1]);
    }

    public function defaultForAccounts()
    {
        return $this->andWhere(['default_for_accounts' => 1]);
    }

    public function defaultForTags()
    {
        return $this->andWhere(['default_for_tags' => 1]);
    }
}
