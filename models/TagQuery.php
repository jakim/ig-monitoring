<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Tag]].
 *
 * @see Tag
 */
class TagQuery extends ActiveQuery
{
    public function monitoring()
    {
        return $this->andWhere(['monitoring' => 1]);
    }
}
