<?php

namespace app\modules\admin;
use yii\filters\AccessControl;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    public function behaviors()
    {
        return [
            'auth' => [
                'class' => AccessControl::class,
                'except' => ['auth/*'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::$app->homeUrl = '/admin/monitoring/accounts';
    }
}
