<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 05.02.2018
 */

namespace app\modules\admin\widgets;


use app\models\Proxy;
use dmstr\widgets\Alert;
use yii\bootstrap\Alert as BootstrapAlert;
use yii\helpers\Html;

class ProxyAlert extends Alert
{

    public function init()
    {
        parent::init();
        $this->options['class'] = $this->alertTypes['danger']['class'];
        $this->options['id'] = $this->getId() . '-danger';
    }

    public function run()
    {
        if (Proxy::find()->active()->exists()) {
            return null;
        }

        $message = Html::a('Add at least one proxy <span class="fa fa-angle-double-right"></span>', ['proxy/create']);

        echo "<section class=\"content-header\">";
        echo BootstrapAlert::widget([
            'body' => $this->alertTypes['warning']['icon'] . $message,
            'closeButton' => false,
            'options' => $this->options,
        ]);
        echo "</section>";
    }
}