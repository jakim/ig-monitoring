<?php

use app\modules\admin\models\Proxy;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tag */
/** @var array|\app\modules\admin\models\Proxy $proxies [] */

$this->title = "{$model->namePrefixed} :: Statistics";
$this->params['breadcrumbs'][] = ['label' => 'Monitoring', 'url' => ['monitoring/tags']];
$this->params['breadcrumbs'][] = ['label' => $model->namePrefixed, 'url' => ['stats', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Statistics';

$formatter = Yii::$app->formatter;
$lastTagStats = $model->lastTagStats;
?>
<div class="tag-view">
    <div class="row">
        <div class="col-lg-3">
            <?= $this->render('_profile', ['model' => $model]) ?>
        </div>
        <div class="col-lg-9">
            <div class="nav-tabs-custom">
                <?= $this->render('_tabs', ['model' => $model]) ?>
                <div class="tab-content">
                    <div class="row">
                        <div class="col-lg-6">
                            <?php $form = ActiveForm::begin(); ?>

                            <?= $form->field($model, 'is_valid')->checkbox() ?>
                            <?= $form->field($model, 'proxy_id')->widget(Select2::class, [
                                'options' => [
                                    'prompt' => 'Select dedicated proxy...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => ArrayHelper::map($proxies, 'id', function (Proxy $proxy) {
                                    return "{$proxy->ip}:{$proxy->port}";
                                }),
                            ]) ?>

                            <div class="form-group">
                                <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
                            </div>

                            <?php ActiveForm::end() ?>
                        </div>
                        <div class="col-lg-4 col-lg-offset-2">
                            <p>
                                <?= Html::a('Delete statistics history', ['tag/delete-stats', 'id' => $model->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'Statistical data will be permanently deleted, are you sure?',
                                    ],
                                ]) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
