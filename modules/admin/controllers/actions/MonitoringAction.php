<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 03.02.2018
 */

namespace app\modules\admin\controllers\actions;


use yii\base\Action;
use yii\helpers\Url;

class MonitoringAction extends Action
{
    public function run($id)
    {
        $model = $this->controller->findModel($id);
        $model->monitoring = $model->monitoring ? 0 : 1;
        $model->update();

        return $this->controller->redirect(Url::previous());
    }
}