<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.06.2018
 */

namespace app\components\traits;


use yii\db\ActiveRecord;

trait FindOrCreate
{
    public function findOrCreate(array $conditions, string $class): ActiveRecord
    {
        /** @var ActiveRecord $class */
        $model = $class::findOne($conditions);
        if ($model === null) {
            $model = new $class($conditions);
        }

        return $model;
    }
}