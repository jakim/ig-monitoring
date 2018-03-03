<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 27.02.2018
 */

namespace app\modules\admin\widgets\favorites;


use app\modules\admin\widgets\base\ModalWidget;
use yii\helpers\Html;
use yii\helpers\Url;

class AddToModal extends ModalWidget
{
    public $modalHeader = 'Add to favorites';
    public $modalToggleButton = [
        'label' => '<span class="fa fa-star text-yellow"></span> Add to favorites',
        'class' => 'btn btn-sm btn-default',
    ];

    protected function renderModalContent()
    {
        echo Html::beginForm();

        echo "<div class=\"form-group\">";
        echo Html::input('text', 'label', null, [
            'placeholder' => 'Name',
            'class' => 'form-control',
            'required' => true,
            'autofocus' => true,
        ]);
        echo Html::hiddenInput('url', Url::current([], true));

        echo "</div>";

        echo Html::submitButton('Add', ['class' => 'btn btn-primary']);

        echo Html::endForm();
    }
}