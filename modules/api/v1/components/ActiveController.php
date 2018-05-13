<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.05.2018
 */

namespace app\modules\api\v1\components;


use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;

class ActiveController extends \yii\rest\ActiveController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'auth' => HttpBearerAuth::class,
        ]);
    }
}