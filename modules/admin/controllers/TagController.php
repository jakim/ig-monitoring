<?php

namespace app\modules\admin\controllers;

use app\components\updaters\TagUpdater;
use app\models\TagStats;
use app\modules\admin\models\Tag;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
                ],
            ],
        ];
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

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getTagStats(),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

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
