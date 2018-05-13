<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

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
                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
                    </div>

                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
