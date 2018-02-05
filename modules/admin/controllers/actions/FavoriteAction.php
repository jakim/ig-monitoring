<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 03.02.2018
 */

namespace app\modules\admin\controllers\actions;


use app\models\Favorite;
use yii\base\Action;
use yii\helpers\Url;

class FavoriteAction extends Action
{
    public function run($id)
    {
        $this->controller->findModel($id);

        $request = \Yii::$app->request;
        $favorite = Favorite::findOne(['label' => $request->post('label')]);
        if ($favorite === null) {
            $favorite = new Favorite();
            $favorite->label = $request->post('label');
            $favorite->url = $request->post('url');
            $favorite->save();
        } else {
            $favorite->delete();
        }

        return $this->controller->redirect(Url::previous());
    }
}