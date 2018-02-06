<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    public function actions()
    {
        return [
            'error' => ErrorAction::class,
        ];
    }

    /**
     * Renders the index view for the module
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
