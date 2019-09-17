<?php

namespace app\modules\admin\controllers;

use app\components\CategoryManager;
use app\components\JobFactory;
use app\components\builders\AccountBuilder;
use app\models\AccountNote;
use app\models\Media;
use app\modules\admin\models\Account;
use app\modules\admin\models\account\MediaAccountSearch;
use app\modules\admin\models\account\MediaTagSearch;
use app\modules\admin\models\account\StatsSearch;
use app\modules\admin\models\AccountStats;
use app\modules\admin\models\Proxy;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii2tech\csvgrid\CsvGrid;

/**
 * AccountController implements the CRUD actions for Account model.
 */
class AccountController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-stats' => ['POST'],
                    'delete-associated' => ['POST'],
                    'categories' => ['POST'],
                    'update-note' => ['POST'],
                    'force-update' => ['POST'],
                ],
            ],
        ];
    }

    public function actionForceUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->is_valid) {
            $accountUpdater = Yii::createObject([
                'class' => AccountBuilder::class,
                'account' => $model,
            ]);
            $accountUpdater->setIsValid()
                ->save();

            $job = JobFactory::updateAccount($model);
            /** @var \yii\queue\Queue $queue */
            $queue = Yii::$app->queue;
            $queue->push($job);
        }
    }

    public function actionDashboard($id)
    {
        $model = $this->findModel($id);

        return $this->render('dashboard', [
            'model' => $model,
        ]);
    }

    public function actionUpdateNote($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user;

        AccountNote::deleteAll([
            'account_id' => $model->id,
            'user_id' => $user->id,
        ]);

        $note = new AccountNote();
        $note->load(Yii::$app->request->post());
        $note->account_id = $model->id;
        $note->user_id = $user->id;
        $note->save();

        return $this->redirect(['account/dashboard', 'id' => $id]);
    }

    public function actionSettings($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Account::SCENARIO_UPDATE);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->is_valid) {
                $accountUpdater = Yii::createObject([
                    'class' => AccountBuilder::class,
                    'account' => $model,
                ]);
                $accountUpdater
                    ->setIsValid()
                    ->setNextStatsUpdate(null)
                    ->save();
            } else {
                $model->save();
            }

            return $this->redirect(['account/dashboard', 'id' => $model->id]);
        }

        $proxies = Proxy::find()
            ->active()
            ->all();

        return $this->render('settings', [
            'model' => $model,
            'proxies' => $proxies,
        ]);
    }

    public function actionDeleteStats($id)
    {
        AccountStats::deleteAll(['account_id' => $id]);

        return $this->redirect(['account/dashboard', 'id' => $id]);
    }

    public function actionDeleteAssociated($id)
    {
        Media::deleteAll(['account_id' => $id]);

        return $this->redirect(['account/dashboard', 'id' => $id]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['monitoring/accounts']);
    }

    public function actionCategories($id)
    {
        /** @var \app\models\User $identity */
        $identity = Yii::$app->user->identity;
        $model = $this->findModel($id);
        $tags = Yii::$app->request->post('account_tags', []);

        $manager = Yii::createObject(CategoryManager::class);
        $manager->saveForAccount($model, $tags, $identity);

        return $this->redirect(['account/dashboard', 'id' => $id]);
    }

    public function actionStats($id)
    {
        $model = $this->findModel($id);

        $searchModel = new StatsSearch();
        $dataProvider = $searchModel->search($model);

        if (Yii::$app->request->get('export')) {
            $csv = new CsvGrid([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'followed_by',
                    'follows',
                    'media',
                    'er',
                    'created_at',
                ],
            ]);

            return $csv->export()->send(sprintf('%s_stats_%s.csv', mb_strtolower($model->username), date('Y-m-d')));
        }

        return $this->render('stats', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMediaTags($id)
    {
        $model = $this->findModel($id);

        $searchModel = new MediaTagSearch();
        $dataProvider = $searchModel->search($model);

        if (Yii::$app->request->get('export')) {
            $csv = new CsvGrid([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'name',
                    'occurs',
                    'ts_avg_likes',
                ],
            ]);

            return $csv->export()->send(sprintf('%s_media-tags_%s.csv', mb_strtolower($model->username), date('Y-m-d')));
        }


        return $this->render('media-tags', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMediaAccounts($id)
    {
        $model = $this->findModel($id);

        $searchModel = new MediaAccountSearch();
        $dataProvider = $searchModel->search($model);

        if (Yii::$app->request->get('export')) {
            $csv = new CsvGrid([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'username',
                    'occurs',
                    'er',
                    'followed_by',
                ],
            ]);

            return $csv->export()->send(sprintf('%s_media-accounts_%s.csv', mb_strtolower($model->username), date('Y-m-d')));
        }

        /** @var \app\models\User $identity */
        $identity = Yii::$app->user->identity;
        $categoryManager = Yii::createObject(CategoryManager::class);
        $categories = $categoryManager->getForUserAccounts($identity, $model);

        return $this->render('media-accounts', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'categories' => $categories,
        ]);
    }


    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Account the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if (($model = Account::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
