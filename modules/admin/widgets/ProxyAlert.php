<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 05.02.2018
 */

namespace app\modules\admin\widgets;


use app\models\Proxy;
use yii\base\Widget;

class ProxyAlert extends Widget
{

    public function run()
    {
        if (!Proxy::find()->active()->exists()) {
            echo "<section class=\"content-header\">";
            echo "<div class=\"alert alert-error\">";
            echo "Add at least one proxy.";
            echo "</div>";
            echo "</section>";
        }
    }
}