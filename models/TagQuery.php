<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Tag]].
 *
 * @see Tag
 */
class TagQuery extends \yii\db\ActiveQuery
{
    public function monitoring()
    {
        return $this->andWhere(['monitoring' => 1]);
    }
}
