<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

$this->title = "{$model->usernamePrefixed} :: Statistics";
$this->params['breadcrumbs'][] = ['label' => 'Monitoring', 'url' => ['monitoring/accounts']];
$this->params['breadcrumbs'][] = ['label' => $model->usernamePrefixed, 'url' => ['dashboard', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Statistics';

$formatter = Yii::$app->formatter;
$lastAccountStats = $model->lastAccountStats;
?>
<div class="account-view">
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

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'is_valid')->checkbox() ?>
                            <?= $form->field($model, 'disabled')->checkbox() ?>

                            <div class="form-group">
                                <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
                            </div>

                            <?php ActiveForm::end() ?>
                        </div>
                        <div class="col-lg-4 col-lg-offset-2">
                            <p>
                                <?= Html::a('Delete statistics history', ['account/delete-stats', 'id' => $model->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'Statistical data will be permanently deleted, are you sure?',
                                    ],
                                ]) ?>
                            </p>
                            <p>
                                <?= Html::a('Delete associated data', ['account/delete-associated', 'id' => $model->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'Associated data (media tags, media accounts) will be permanently deleted, are you sure?',
                                    ],
                                ]) ?>
                            </p>
                            <p>
                                <?= Html::a('Delete account', ['account/delete', 'id' => $model->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'Account and all associated data will be permanently deleted, are you sure?',
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
