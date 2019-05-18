<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 09.07.2018
 */

namespace app\components\traits;


use Yii;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

trait SaveModelTrait
{
    protected function saveModel(ActiveRecord $model)
    {
        if (!$model->save()) {
            Yii::error($model->errors, __METHOD__);
            throw new ServerErrorHttpException('Something went wrong.');
        }
    }
}