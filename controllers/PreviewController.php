<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 20.04.2018
 */

namespace app\controllers;


use app\models\Account;
use app\modules\admin\components\AccountStatsManager;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PreviewController extends Controller
{
    public function actionAccount($uid)
    {
        $account = Account::findOne(['uid' => $uid, 'monitoring' => 1]);
        if ($account === null) {
            throw new NotFoundHttpException();
        }

        $manager = \Yii::createObject([
            'class' => AccountStatsManager::class,
            'account' => $account,
        ]);

        return $this->render('account', [
            'model' => $account,
            'manager' => $manager,

        ]);
    }

}