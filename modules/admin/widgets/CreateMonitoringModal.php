<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.02.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use app\dictionaries\ProxyType;
use app\models\Proxy;
use app\modules\admin\widgets\base\ModalWidget;
use kartik\select2\Select2;
use yii\helpers\Html;

class CreateMonitoringModal extends ModalWidget
{
    public $modalHeader = 'Create account monitoring';
    public $modalToggleButton = [
        'label' => 'Create',
    ];

    protected function renderModalContent()
    {
        echo Html::beginForm('create-account');

        echo "<div class=\"form-group\">";
        echo Html::input('text', 'username', null, [
            'placeholder' => 'Username',
            'class' => 'form-control',
            'required' => true,
            'autofocus' => true,
        ]);
        echo "</div>";

        echo "<div class=\"form-group\">";
        echo Select2::widget([
            'name' => 'proxy_id',
            'options' => [
                'placeholder' => 'Select proxy...',
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
            'data' => ArrayHelper::map(Proxy::find()->where(['type' => ProxyType::ACCOUNT])->all(), 'id', 'ip'),
        ]);
        echo "</div>";

        echo Html::submitButton('Create', ['class' => 'btn btn-primary']);

        echo Html::endForm();
    }
}