<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

$this->title = "{$model->usernamePrefixed} :: Media Accounts";
$this->params['breadcrumbs'][] = ['label' => 'Monitoring', 'url' => ['monitoring/accounts']];
$this->params['breadcrumbs'][] = ['label' => $model->usernamePrefixed, 'url' => ['dashboard', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Media Accounts';

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
                    <?= \yii\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => \yii\grid\SerialColumn::class],
                            [
                                'attribute' => 'username',
                                'format' => 'html',
                                'value' => function (\app\models\Account $model, $key, $index, $column) {
                                    if ($model->monitoring) {
                                        return Html::a($model->usernamePrefixed, ['account/stats', 'id' => $model->id]);
                                    }

                                    return $model->{$column->attribute};
                                },
                            ],
                            'occurs',
                            [
                                'format' => 'raw',
                                'value' => function (\app\models\Account $model) {
                                    return \app\modules\admin\widgets\MonitoringButton::widget([
                                        'model' => $model,
                                        'linkCssClass' => 'btn btn-xs',
                                    ]);
                                },
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
            <div class="alert alert-info">
                <span class="fa fa-info-circle"></span> Obtained from media captions.
            </div>
        </div>
    </div>
</div>
