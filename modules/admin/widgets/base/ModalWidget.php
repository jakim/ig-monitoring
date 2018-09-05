<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 03.03.2018
 */

namespace app\modules\admin\widgets\base;


use app\components\ArrayHelper;
use yii\base\Widget;
use yii\bootstrap\Modal;

abstract class ModalWidget extends Widget
{
    public $modalHeader;
    public $modalToggleButton = [];

    public function init()
    {
        $this->view->registerJs('$(document).on(\'shown.bs.modal\', function (e) {$(\'[autofocus]\', e.target).focus();});');
        parent::init();
    }

    public function run()
    {
        $this->renderModal();
    }

    protected function renderModal()
    {
        Modal::begin([
            'header' => "<h4 class=\"modal-title\">{$this->modalHeader}</h4>",
            'toggleButton' => ArrayHelper::merge([
                'tag' => 'a',
                'label' => 'Modal',
                'class' => 'btn btn-sm btn-success',
            ], $this->modalToggleButton),
            'options' => [
                'class' => 'text-left fade'
            ],
        ]);

        $this->renderModalContent();

        Modal::end();
    }

    abstract protected function renderModalContent();
}