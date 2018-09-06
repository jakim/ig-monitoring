<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 27.02.2018
 */

namespace app\modules\admin\widgets\favorites;


use app\models\Favorite;
use app\modules\admin\widgets\base\ModalWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

class AddToModal extends ModalWidget
{
    public $modalHeader = 'Add to favorites';
    public $modalToggleButton = [
        'label' => '<span class="fa fa-star text-yellow"></span> Add list to favorites',
        'class' => 'btn btn-sm btn-default',
    ];

    protected function renderModalContent()
    {
        $model = new Favorite();
        $form = ActiveForm::begin([
            'action' => ['favorite/create'],
        ]);
        echo $form->field($model, 'label')->textInput([
            'placeholder' => 'Name',
            'required' => true,
            'autofocus' => true,
        ]);
        echo $form->field($model, 'url')->hiddenInput(['value' => Url::current()])->label(false);

        echo Html::hiddenInput('prefix', '<span class=\'fa fa-search\'></span> ');

        echo Html::submitButton('Save', ['class' => 'btn btn-primary']);

        ActiveForm::end();
    }
}