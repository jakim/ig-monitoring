<?php

namespace app\modules\admin\controllers;

use app\components\JobFactory;
use app\components\updaters\TagUpdater;
use app\models\TagStats;
use app\modules\admin\models\Tag;
use app\modules\admin\models\tag\StatsSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii2tech\csvgrid\CsvGrid;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends Controller
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
                    'monitoring' => ['POST'],
                    'delete-stats' => ['POST'],
                    'force-update' => ['POST'],
                ],
            ],
        ];
    }

    public function actionForceUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->is_valid) {
            $tagUpdater = Yii::createObject([
                'class' => TagUpdater::class,
                'tag' => $model,
            ]);
            $tagUpdater->setIsValid()
                ->save();

            $job = JobFactory::createTagUpdate($model);
            /** @var \yii\queue\Queue $queue */
            $queue = Yii::$app->queue;
            $queue->push($job);
        }
    }

    public function actionDeleteStats($id)
    {
        TagStats::deleteAll(['account_id' => $id]);

        return $this->redirect(['tag/stats', 'id' => $id]);
    }

    public function actionSettings($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Tag::SCENARIO_UPDATE);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->disabled) {
                $model->monitoring = 0;
                $model->save();
            } elseif ($model->is_valid) {
                $tagUpdater = Yii::createObject([
                    'class' => TagUpdater::class,
                    'tag' => $model,
                ]);
                $tagUpdater
                    ->setIsValid()
                    ->setNextStatsUpdate(null)
                    ->save();
            } else {
                $model->save();
            }

            return $this->redirect(['tag/stats', 'id' => $model->id]);
        }

        return $this->render('settings', [
            'model' => $model,
        ]);
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
                    'media',
                    'likes',
                    'min_likes',
                    'max_likes',
                    'comments',
                    'min_comments',
                    'max_comments',
                    'created_at',
                ],
            ]);

            return $csv->export()->send(sprintf('%s_stats_%s.csv', mb_strtolower($model->slug), date('Y-m-d')));
        }

        return $this->render('stats', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Tag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tag::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
