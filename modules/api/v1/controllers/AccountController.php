<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.05.2018
 */

namespace app\modules\api\v1\controllers;


use app\modules\api\v1\components\ActiveController;
use app\modules\api\v1\models\Account;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class AccountController extends ActiveController
{
    public $modelClass = Account::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);

        return $actions;
    }

    public function actionCreate()
    {
        $username = \Yii::$app->request->post('username');

        $model = Account::findOne(['username' => $username]);

        if ($model === null) {
            $model = new Account();
            $model->setScenario($this->createScenario);
        }
        $isNewRecord = $model->isNewRecord;

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode($isNewRecord ? 201 : 200);

            // TODO uncomment after create "view" endpoint
//            $id = implode(',', array_values($model->getPrimaryKey(true)));
//            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));

        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}