<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 27.02.2018
 */

namespace app\modules\admin\controllers;


use app\models\Favorite;
use yii\web\Controller;

class FavoriteController extends Controller
{
    public function actionDelete($id)
    {
        Favorite::deleteAll(['id' => $id]);
    }
}