<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\controllers;


use app\components\AccountManager;
use app\components\CategoryManager;
use app\components\JobFactory;
use app\components\stats\AccountDailyDiff;
use app\components\stats\AccountMonthlyDiff;
use app\components\stats\TagDailyDiff;
use app\components\stats\TagMonthlyDiff;
use app\components\TagManager;
use app\dictionaries\TrackerType;
use app\models\Account;
use app\models\Tag;
use app\modules\admin\models\AccountSearch;
use app\modules\admin\models\MonitoringForm;
use app\modules\admin\models\TagSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\Url;
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
                    'delete-account' => ['POST'],
                    'create-tag' => ['POST'],
                    'delete-tag' => ['POST'],
                ],
            ],
        ];
    }

    public function actionAccounts()
    {
        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['account.monitoring' => 1]);

        $dailyDiff = Yii::createObject([
            'class' => AccountDailyDiff::class,
            'models' => $dataProvider->models,
        ]);
        $dailyDiff->initLastDiff();

        $monthlyDiff = Yii::createObject([
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

    public function actionTags()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['tag.monitoring' => 1]);

        $dailyDiff = Yii::createObject([
            'class' => TagDailyDiff::class,
            'models' => $dataProvider->models,
        ]);
        $dailyDiff->initLastDiff();

        $monthlyDiff = Yii::createObject([
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

    public function actionCreateAccount()
    {
        $form = new MonitoringForm();
        $form->setScenario(TrackerType::ACCOUNT);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $usernames = $this->normalizeTrackers($form->names);

            $accountManager = Yii::createObject(AccountManager::class);

            /** @var \yii\queue\Queue $queue */
            $queue = Yii::$app->queue;

            foreach ($usernames as $username) {
                $account = $accountManager->startMonitoring($username, $form->proxy_id);
                $account->disabled = 0;
                if (!$account->hasErrors()) {
                    Yii::$app->session->setFlash('success', 'OK!');

                    $job = JobFactory::createAccountUpdate($account);
                    $queue->push($job);

                    $categories = array_filter((array)$form->categories);
                    if ($categories) {
                        /** @var \app\models\User $identity */
                        $identity = Yii::$app->user->identity;
                        $categoryManager = \Yii::createObject(CategoryManager::class);
                        $categoryManager->addToAccount($account, $categories, $identity);
                    }
                } else {
                    Yii::error('Validation error: ' . json_encode($account->errors), __METHOD__);
                    Yii::$app->session->setFlash('error', "ERR! {$username}");
                    break;
                }

            }
        }

        return $this->redirect(['monitoring/accounts', 'sort' => '-created_at']);
    }

    public function actionCreateTag()
    {
        $form = new MonitoringForm();
        $form->setScenario(TrackerType::TAG);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $names = $this->normalizeTrackers($form->names);

            $tagManager = Yii::createObject(TagManager::class);

            /** @var \yii\queue\Queue $queue */
            $queue = Yii::$app->queue;

            foreach ($names as $name) {
                $tag = $tagManager->startMonitoring($name, $form->proxy_id);
                if (!$tag->hasErrors()) {
                    Yii::$app->session->setFlash('success', 'OK!');
                    $job = JobFactory::createTagUpdate($tag);
                    $queue->push($job);
                } else {
                    Yii::error('Validation error: ' . json_encode($tag->errors), __METHOD__);
                    Yii::$app->session->setFlash('error', "ERR! {$name}");
                    break;
                }

            }
        }

        return $this->redirect(['monitoring/tags', 'sort' => '-created_at']);
    }

    public function actionDeleteAccount($id)
    {
        $model = Account::findOne($id);
        $model->monitoring = 0;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'OK!');

            return Url::to(['monitoring/accounts']);
        } else {
            Yii::$app->session->setFlash('error', 'ERROR!');
        }
    }

    public function actionDeleteTag($id)
    {
        $model = Tag::findOne($id);
        $model->monitoring = 0;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'OK!');

            return Url::to(['monitoring/tags']);
        } else {
            Yii::$app->session->setFlash('error', 'ERROR!');
        }
    }

    /**
     * @param string $trackers
     * @return array
     */
    private function normalizeTrackers(string $trackers): array
    {
        $trackers = StringHelper::explode($trackers, ',', true, true);
        $trackers = array_unique($trackers);

        return $trackers;
    }
}