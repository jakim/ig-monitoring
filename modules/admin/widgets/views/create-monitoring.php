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
 * @var array|false $tags
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
if ($tags !== false) {
    echo $form->field($model, 'tags')->widget(Select2::class, [
        'options' => [
            'id' => "tags_{$form->getId()}",
            'multiple' => true,
            'placeholder' => 'Select tags...',
        ],
        'pluginOptions' => [
            'tags' => true,
        ],
        'data' => $tags,
    ])->label(false);
}
?>

    <div class="panel panel-default">
        <div class="panel-heading">Proxy settings</div>
        <div class="panel-body">
            <?= $form->field($model, 'proxy_id')->widget(Select2::class, [
                'data' => $proxies,
                'options' => [
                    'id' => "proxy_id_{$form->getId()}",
                    'placeholder' => 'Select dedicated proxy...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ])->label(false) ?>
            <div class="form-group-sm">or</div>
            <div class="well well-sm">
                leave empty if you want to use the random one
            </div>
        </div>
    </div>

<?= Html::submitButton('Create', ['class' => 'btn btn-primary']); ?>

<?php

ActiveForm::end();
