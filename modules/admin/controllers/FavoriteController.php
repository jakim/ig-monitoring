<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 27.02.2018
 */

namespace app\modules\admin\controllers;


use app\models\Favorite;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class FavoriteController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verb' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'create' => ['POST'],
                ],
            ],
        ]);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Favorite();
        $model->load($request->post(), $request->isAjax ? '' : null);
        $model->user_id = Yii::$app->user->id;
        $model->label = $request->post('prefix', '') . $model->label;

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'OK!');
        } else {

            Yii::$app->session->setFlash('error', 'ERROR!');
        }

        if ($request->isAjax) {
            return $model->url;
        }

        return $this->redirect($model->url);
    }

    public function actionDelete($id)
    {
        Favorite::deleteAll([
            'user_id' => Yii::$app->user->id,
            'id' => $id,
        ]);
    }
}