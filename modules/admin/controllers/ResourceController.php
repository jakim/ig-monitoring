<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 07.04.2018
 */

namespace app\modules\admin\controllers;


use yii\web\Controller;

class ResourceController extends Controller
{
    public function actionProxy()
    {
        return $this->redirect('https://billing.blazingseollc.com/hosting/aff.php?aff=303');
    }
}