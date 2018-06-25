<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 20.04.2018
 */

namespace app\controllers;

use Yii;
use app\models\Account;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\stats\AccountDailyDiff;
use app\components\stats\AccountMonthlyDiff;
use Carbon\Carbon;
use app\components\stats\AccountDaily;

class PreviewController extends Controller
{
    public function actionAccount($uid)
    {
        $model = Account::findOne(['uid' => $uid, 'monitoring' => 1]);
        if ($model === null) {
            throw new NotFoundHttpException();
        }

        $dailyDiff = Yii::createObject([
            'class' => AccountDailyDiff::class,
            'models' => $model,
        ]);
        $dailyDiff->initDiff(Carbon::now()->subMonth());
        $dailyChanges = $dailyDiff->getDiff($model->id);
        $lastDailyChange = end($dailyChanges);

        $monthlyDiff = Yii::createObject([
            'class' => AccountMonthlyDiff::class,
            'models' => $model,
        ]);
        $monthlyDiff->initDiff(Carbon::now()->subYear());
        $monthlyChanges = $monthlyDiff->getDiff($model->id);
        $lastMonthlyChange = end($monthlyChanges);

        $dailyStats = Yii::createObject(AccountDaily::class, [$model]);
        $dailyStats->initDiff(Carbon::now()->subMonth());
        $dailyStats = $dailyStats->get();

        return $this->render('account', [
            'model' => $model,
            'lastDailyChange' => $lastDailyChange,
            'dailyChanges' => $dailyChanges,
            'dailyStats' => $dailyStats,
            'lastMonthlyChange' => $lastMonthlyChange,
            'monthlyChanges' => $monthlyChanges,

        ]);
    }

}