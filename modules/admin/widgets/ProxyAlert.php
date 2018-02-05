<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 05.02.2018
 */

namespace app\modules\admin\widgets;


use app\dictionaries\ProxyType;
use app\models\Proxy;
use yii\base\Widget;
use yii\helpers\Html;

class ProxyAlert extends Widget
{
    protected $items;

    public function init()
    {
        parent::init();
        $this->items = Proxy::find()
            ->select('type')
            ->indexBy('type')
            ->groupBy('type')
            ->asArray()
            ->all();
    }

    public function run()
    {
        if ($this->items) {
            return null;
        }

        echo "<section class=\"content-header\">";
        echo "<div class=\"alert alert-error\">";
        echo $this->renderContent();
        echo "</div>";
        echo "</section>";
    }

    private function renderContent()
    {
        $items = [];
        if (!isset($this->items[ProxyType::ACCOUNT])) {
            $items[] = 'Add at least one proxy for accounts.';
        }
        if (!isset($this->items[ProxyType::TAG])) {
            $items[] = 'Add at least one proxy for tags.';
        }

        return Html::ul($items);
    }
}