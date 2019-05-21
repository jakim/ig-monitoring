<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.03.2018
 */

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var \app\modules\admin\models\MonitoringForm $model
 * @var array|string $formAction
 * @var string $title
 * @var array|false $categories
 * @var array $proxies
 */

$form = ActiveForm::begin([
    'action' => $formAction,
]);
?>
<?= $form->field($model, 'names')
    ->textInput(['maxlength' => true, 'placeholder' => true])
    ->label(false);
?>

<?php
if ($categories !== false) {
    echo $form->field($model, 'categories')->widget(Select2::class, [
        'options' => [
            'id' => "categories_{$form->getId()}",
            'multiple' => true,
            'placeholder' => 'Select categories...',
        ],
        'pluginOptions' => [
            'categories' => true,
        ],
        'data' => $categories,
    ])->label(false);
}
?>

<?= Html::submitButton('Create', ['class' => 'btn btn-primary']); ?>

<?php

ActiveForm::end();
