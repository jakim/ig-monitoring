<?php

namespace app\modules\admin\controllers;

use app\components\AccountManager;
use app\models\Tag;
use app\modules\admin\components\AccountStatsManager;
use app\modules\admin\controllers\actions\FavoriteAction;
use app\modules\admin\controllers\actions\MonitoringAction;
use app\modules\admin\models\Account;
use Yii;
use app\modules\admin\models\AccountSearch;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
                    'monitoring' => ['POST'],
                    'tags' => ['POST'],
                    'favorite' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'favorite' => FavoriteAction::class,
            'monitoring' => MonitoringAction::class,
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (in_array($this->action->id, ['stats', 'media-tags', 'media-accounts'])) {
                Url::remember(Url::current());
            }

            return true;
        }

        return false;
    }

    public function actionTags($id)
    {
        $model = $this->findModel($id);
        $tags = Yii::$app->request->post('account_tags', []);

        $manager = Yii::createObject(AccountManager::class);
        $manager->updateTags($model, $tags);

        return $this->redirect(Url::previous());
    }

    /**
     * Lists all Account models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDashboard($id)
    {
        $model = $this->findModel($id);

        $manager = Yii::createObject([
            'class' => AccountStatsManager::class,
            'account' => $model,
        ]);

        return $this->render('dashboard', [
            'manager' => $manager,
            'model' => $model,
        ]);
    }

    public function actionStats($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getAccountStats(),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        return $this->render('stats', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMediaTags($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => Tag::find()
                ->select([
                    'tag.*',
                    'count(tag.id) as occurs',
                ])
                ->innerJoinWith(['media' => function(Query $q) use ($model) {
                    $q->andWhere(['media.account_id' => $model->id]);
                }])
                ->groupBy('tag.id'),
        ]);

        $dataProvider->sort->attributes['occurs'] = [
            'asc' => ['occurs' => SORT_ASC],
            'desc' => ['occurs' => SORT_DESC],
        ];
        $dataProvider->sort->defaultOrder = [
            'occurs' => SORT_DESC,
            'name' => SORT_ASC,
        ];


        return $this->render('media-tags', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMediaAccounts($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => Account::find()
                ->select([
                    'account.*',
                    'count(account.id) as occurs',
                ])
                ->innerJoinWith(['mediaAccounts.media' => function(Query $q) use ($model) {
                    $q->andWhere(['media.account_id' => $model->id]);
                }])
                ->groupBy('account.id'),
        ]);

        $dataProvider->sort->attributes['occurs'] = [
            'asc' => ['occurs' => SORT_ASC],
            'desc' => ['occurs' => SORT_DESC],
        ];
        $dataProvider->sort->defaultOrder = [
            'occurs' => SORT_DESC,
            'username' => SORT_ASC,
        ];


        return $this->render('media-accounts', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Account model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Account();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['account/dashboard', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Account model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['account/dashboard', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Account model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
