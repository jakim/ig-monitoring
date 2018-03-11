<?php

namespace app\modules\admin\controllers;

use app\components\ArrayHelper;
use app\models\ProxyTag;
use app\modules\admin\models\Proxy;
use app\modules\admin\models\Tag;
use Yii;
use app\modules\admin\models\ProxySearch;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProxyController implements the CRUD actions for Proxy model.
 */
class ProxyController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * Lists all Proxy models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProxySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new Proxy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Proxy();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->tagString) {
                $this->saveTags($model);
            }

            return $this->redirect(['proxy/index']);
        }

        return $this->render('create', [
            'model' => $model,
            'selectData' => $this->selectData(),
        ]);
    }

    /**
     * Updates an existing Proxy model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ProxyTag::deleteAll(['proxy_id' => $model->id]);
            if ($model->tagString) {
                $this->saveTags($model);
            }

            return $this->redirect(['proxy/index']);
        }

        $model->tagString = ArrayHelper::getColumn($model->getTags()->all(), 'name');

        return $this->render('update', [
            'model' => $model,
            'selectData' => $this->selectData(),
        ]);
    }

    /**
     * Deletes an existing Proxy model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Proxy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Proxy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Proxy::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function saveTags(Proxy $model): void
    {
        foreach ($model->tagString as $name) {
            $tag = Tag::findOne(['slug' => Inflector::slug($name)]);
            if ($tag === null) {
                $tag = new Tag(['name' => $name]);
            }
            if ($tag->save()) {
                $model->link('tags', $tag);
            }
        }
    }

    /**
     * @return array
     */
    private function selectData(): array
    {
        return \app\models\Tag::find()
            ->indexBy('name')
            ->select('name')
            ->innerJoin('proxy_tag', 'tag.id=proxy_tag.tag_id')
            ->column();
    }
}
