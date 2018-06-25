<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\controllers;


use app\components\AccountManager;
use app\components\stats\AccountDailyDiff;
use app\components\stats\AccountMonthlyDiff;
use app\components\stats\TagDailyDiff;
use app\components\stats\TagMonthlyDiff;
use app\components\TagManager;
use app\models\Favorite;
use app\modules\admin\models\Account;
use app\modules\admin\models\AccountMonitoringForm;
use app\modules\admin\models\AccountSearch;
use app\modules\admin\models\Tag;
use app\modules\admin\models\TagMonitoringForm;
use app\modules\admin\models\TagSearch;
use Carbon\Carbon;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
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

    public function actionCreateTag()
    {
        $form = new TagMonitoringForm();

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $names = StringHelper::explode($form->names, ',', true, true);

            $tagManager = \Yii::createObject(TagManager::class);

            foreach ($names as $name) {
                $tag = $tagManager->monitor($name, $form->proxy_id, $form->proxy_tag_id);
                if (!$tag->hasErrors()) {
                    \Yii::$app->session->setFlash('success', 'OK!');
                } else {
                    \Yii::error('Validation error: ' . json_encode($tag->errors), __METHOD__);
                    \Yii::$app->session->setFlash('error', 'ERR!');
                }

            }
        }

        return $this->redirect(['monitoring/tags', 'sort' => '-created_at']);
    }

    public function actionCreateAccount()
    {
        $form = new AccountMonitoringForm();

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $usernames = StringHelper::explode($form->names, ',', true, true);

            $accountManager = \Yii::createObject(AccountManager::class);

            foreach ($usernames as $username) {
                $account = $accountManager->monitor($username, $form->proxy_id, $form->proxy_tag_id);
                if (!$account->hasErrors()) {
                    \Yii::$app->session->setFlash('success', 'OK!');
                    $tagManager = \Yii::createObject(TagManager::class);
                    $tagManager->saveForAccount($account, (array)$form->tags);
                } else {
                    \Yii::error('Validation error: ' . json_encode($account->errors), __METHOD__);
                    \Yii::$app->session->setFlash('error', "ERR! {$username}");
                    break;
                }

            }
        }

        return $this->redirect(['monitoring/accounts', 'sort' => '-created_at']);
    }

    public function actionTags()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['tag.monitoring' => 1]);

        $dailyDiff = \Yii::createObject([
            'class' => TagDailyDiff::class,
            'models' => $dataProvider->models,
        ]);
        $dailyDiff->initLastDiff();

        $monthlyDiff = \Yii::createObject([
            'class' => TagMonthlyDiff::class,
            'models' => $dataProvider->models,
        ]);
        $monthlyDiff->initLastDiff();

        return $this->render('tags', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dailyDiff' => $dailyDiff,
            'monthlyDiff' => $monthlyDiff,
        ]);
    }

    public function actionAccounts()
    {

        $label = \Yii::$app->request->post('label');
        $url = \Yii::$app->request->post('url');
        if ($label && $url) {
            (new Favorite([
                'url' => $url,
                'label' => "<span class='fa fa-search'></span> $label",
            ]))->insert(false);
        }

        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['account.monitoring' => 1]);

        $dailyDiff = \Yii::createObject([
            'class' => AccountDailyDiff::class,
            'models' => $dataProvider->models,
        ]);
        $dailyDiff->initLastDiff();

        $monthlyDiff = \Yii::createObject([
            'class' => AccountMonthlyDiff::class,
            'models' => $dataProvider->models,
        ]);
        $monthlyDiff->initLastDiff();

        return $this->render('accounts', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dailyDiff' => $dailyDiff,
            'monthlyDiff' => $monthlyDiff,
        ]);
    }
}