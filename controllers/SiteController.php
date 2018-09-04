<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 04.09.2018
 */

namespace app\controllers;


use yii\web\Controller;

class SiteController extends Controller
{
    public function actionTerms()
    {
        return $this->render('terms');
    }

    public function actionPrivacy()
    {
        return $this->render('privacy');
    }
}