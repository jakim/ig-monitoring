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
 * @var \app\modules\admin\models\AccountMonitoringForm $model
 * @var array $accountTags
 * @var array $proxies
 * @var array $proxyTags
 */

$form = ActiveForm::begin([
    'action' => ['monitoring/create-account'],
]);
?>
    <div class="panel panel-primary">
        <div class="panel-heading">Accounts</div>
        <div class="panel-body">
            <?= $form->field($model, 'usernames')
                ->textInput(['maxlength' => true, 'placeholder' => true])
                ->label(false);
            ?>

            <?= $form->field($model, 'tags')->widget(Select2::class, [
                'options' => [
                    'multiple' => true,
                    'placeholder' => 'Select tags...',
                ],
                'pluginOptions' => [
                    'tags' => true,
                ],
                'data' => $accountTags,
            ])->label(false);

            ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Proxy settings</div>
        <div class="panel-body">
            <?= $form->field($model, 'proxy_id')->widget(Select2::class, [
                'data' => $proxies,
                'options' => [
                    'placeholder' => 'Select dedicated proxy...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ])->label(false) ?>
            <div class="form-group-sm">or</div>
            <?= $form->field($model, 'proxy_tag_id')->widget(Select2::class, [
                'data' => $proxyTags,
                'options' => [
                    'placeholder' => 'Select proxy tag...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ])->label(false) ?>
            <div class="form-group-sm">or</div>
            <div class="well well-sm">
                leave empty if you want to use the default one
            </div>
        </div>
    </div>

<?= Html::submitButton('Create', ['class' => 'btn btn-primary']); ?>

<?php

ActiveForm::end();
