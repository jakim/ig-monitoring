<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 29.06.2018
 */

namespace app\modules\admin\widgets;


use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class PoweredBy extends Widget
{
    public function run()
    {
        $params = \Yii::$app->params;
        $name = ArrayHelper::getValue($params, 'app.name', \Yii::$app->name);
        $homepage = ArrayHelper::getValue($params, 'app.homepage', 'https://github.com/jakim/ig-monitoring');
        $slogan = ArrayHelper::getValue($params, 'app.slogan', 'Free, self hosted Instagram Analytics and Stats.');


        return sprintf('%s - %s', Html::a($name, $homepage), Html::encode($slogan));
    }
}