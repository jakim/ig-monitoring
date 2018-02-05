<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\controllers;


use app\modules\admin\models\AccountSearch;
use app\modules\admin\models\TagSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class MonitoringController extends Controller
{
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