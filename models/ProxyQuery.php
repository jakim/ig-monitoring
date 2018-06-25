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
}
