<?php

namespace app\modules\api\v1;

use yii\filters\auth\HttpBearerAuth;

/**
 * v1 module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\v1\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::$app->errorHandler->errorAction = '/v1/default/error';
    }
}
