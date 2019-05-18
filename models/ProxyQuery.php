<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Proxy]].
 *
 * @see Proxy
 */
class ProxyQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['active' => 1]);
    }
}
