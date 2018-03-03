<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 27.02.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use jakim\ig\Url;
use app\dictionaries\ProxyType;
use app\models\Proxy;
use kartik\select2\Select2;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;

class FavoriteModalWidget extends Widget
{
    public function init()
    {
        $this->view->registerJs('$(document).on(\'shown.bs.modal\', function (e) {$(\'[autofocus]\', e.target).focus();});');
        parent::init();
    }

    public function run()
    {
        Modal::begin([
            'header' => 'Add to favorites',
            'toggleButton' => [
                'tag' => 'a',
                'label' => '<span class="fa fa-star text-yellow"></span> Add to favorites',
                'class' => 'btn btn-sm btn-default',
            ],
        ]);
        echo Html::beginForm();

        echo "<div class=\"form-group\">";
        echo Html::input('text', 'label', null, [
            'placeholder' => 'Name',
            'class' => 'form-control',
            'required' => true,
            'autofocus' => true,
        ]);
        echo Html::hiddenInput('url', \yii\helpers\Url::current([], true));

        echo "</div>";

        echo Html::submitButton('Add', ['class' => 'btn btn-primary']);

        echo Html::endForm();
        Modal::end();
    }
}