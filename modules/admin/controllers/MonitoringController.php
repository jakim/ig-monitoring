<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\controllers;


use app\dictionaries\ProxyType;
use app\models\Proxy;
use app\modules\admin\models\Account;
use app\modules\admin\models\AccountSearch;
use app\modules\admin\models\TagSearch;
use yii\filters\VerbFilter;
use yii\web\Controller;

class MonitoringController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create-account' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreateAccount()
    {
        $request = \Yii::$app->request;
        $username = $request->post('username');

        $account = Account::findOne(['username' => $username]);
        if ($account === null) {
            $account = new Account(['username' => $username]);
        }
        $account->monitoring = 1;

        $proxy = Proxy::findOne(['id' => $request->post('proxy_id'), 'type' => ProxyType::ACCOUNT]);
        $account->proxy_id = $proxy ? $proxy->id : null;

        if ($account->save()) {
            \Yii::$app->session->setFlash('success', 'OK!');
        } else {
            \Yii::error('Validation error: ' . json_encode($account->errors), __METHOD__);
            \Yii::$app->session->setFlash('error', 'ERR!');
        }

        return $this->redirect(['account/stats', 'id' => $account->id]);
    }

    public function actionTags()
    {
        $searchModel = new TagSearch(['monitoring' => 1]);
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('tags', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAccounts()
    {
        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['account.monitoring' => 1]);

        return $this->render('accounts', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}