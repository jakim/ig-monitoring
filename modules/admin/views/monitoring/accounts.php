<?php

use jakim\ig\Url;
use app\modules\admin\components\grid\StatsColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\CreateMonitoringModal;
use app\modules\admin\widgets\favorites\AddToModal;

/**
 * @var yii\web\View $this
 * @var app\modules\admin\models\AccountSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $dailyDiff
 * @var array $monthlyDiff
 */

$this->title = 'Monitoring :: Accounts';
$this->params['breadcrumbs'][] = 'Monitoring';
$this->params['breadcrumbs'][] = 'Accounts';

/** @var \app\components\Formatter $formatter */
$formatter = Yii::$app->formatter;
?>
    <div class="account-index nav-tabs-custom">

        <?= $this->render('_tabs') ?>

        <div class="tab-content table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'emptyText' => sprintf('<p class="lead">This is a screen where you can compare metrics for multiple accounts.</p>%s',
                    CreateMonitoringModal::widget([
                        'modalToggleButton' => [
                            'label' => 'Add first account',
                            'class' => 'btn btn-lg btn-success',
                        ],
                    ])),
                'emptyTextOptions' => [
                    'class' => 'text-center empty',
                ],
                'columns' => [
                    ['class' => \yii\grid\SerialColumn::class],
                    [
                        'attribute' => 'username',
                        'content' => function (\app\models\Account $model) {
                            $html = [];
                            $html[] = Html::a($model->displayName, ['account/dashboard', 'id' => $model->id]);
                            $html[] = Html::a('<span class="fa fa-external-link text-sm"></span>', Url::account($model->username), ['target' => '_blank']);

                            if (!$model->is_valid) {
                                $html[] = sprintf(
                                    '<span class="fa fa-exclamation-triangle text-danger pull-right" data-toggle="tooltip" data-placement="top"  title="%s, attempts: %s"></span>',
                                    \app\dictionaries\AccountInvalidationType::getLabel($model->invalidation_type_id, 'Unknown reason'),
                                    $model->invalidation_count
                                );
                            }
                            if ($model->accounts_monitoring_level) {
                                $html[] = sprintf('<span class="fa fa-magic text-muted pull-right" title="monitoring level: %s"></span>', $model->accounts_monitoring_level);
                            }
                            if ($model->disabled) {
                                $html[] = '<span class="fa fa-exclamation-triangle text-danger pull-right" title="Not found."></span>';
                            }

                            return implode(" \n", $html);
                        },
                    ],
                    [
                        'class' => StatsColumn::class,
                        'attribute' => 'followed_by',
                        'dailyDiff' => $dailyDiff,
                        'monthlyDiff' => $monthlyDiff,
                    ],
                    [
                        'class' => StatsColumn::class,
                        'attribute' => 'follows',
                        'dailyDiff' => $dailyDiff,
                        'monthlyDiff' => $monthlyDiff,
                    ],
                    [
                        'class' => StatsColumn::class,
                        'attribute' => 'media',
                        'dailyDiff' => $dailyDiff,
                        'monthlyDiff' => $monthlyDiff,
                    ],
                    [
                        'class' => StatsColumn::class,
                        'attribute' => 'er',
                        'dailyDiff' => $dailyDiff,
                        'monthlyDiff' => $monthlyDiff,
                        'numberFormat' => [
                            'percent',
                            2,
                            ['sign' => false],
                        ],
                    ],
                    's_categories',
                    [
                        'attribute' => 'stats_updated_at',
                        'label' => 'Updated At',
                        'format' => 'date',
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'date',
                    ],
                ],
            ]); ?>

            <?php if ($dataProvider->totalCount): ?>
                <?= CreateMonitoringModal::widget([
                    'modalToggleButton' => ['label' => 'Add accounts'],
                ]) ?>
                <?= AddToModal::widget() ?>
            <?php endif; ?>
        </div>
    </div>

<?php
$this->registerJs('jQuery(\'[data-toggle="tooltip"]\').tooltip()');
