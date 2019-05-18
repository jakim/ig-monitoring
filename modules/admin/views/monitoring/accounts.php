<?php

use app\modules\admin\components\grid\AccountColumn;
use app\modules\admin\components\grid\GridView;
use app\modules\admin\components\grid\StatsColumn;
use app\modules\admin\widgets\CreateMonitoringModal;
use app\modules\admin\widgets\favorites\AddToModal;
use yii\grid\SerialColumn;

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

        <div class="tab-content">

            <?php if ($dataProvider->totalCount): ?>
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-lg-12 text-right">
                        <?= CreateMonitoringModal::widget([
                            'modalToggleButton' => ['label' => 'Add accounts'],
                        ]) ?>
                        <?= AddToModal::widget() ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
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
                        ['class' => SerialColumn::class],
                        [
                            'class' => AccountColumn::class,
                            'attribute' => 'username',
                            'displayDashboardLink' => true,
                            'visible' => true,
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
            </div>
        </div>
    </div>

<?php
$this->registerJs('jQuery(\'[data-toggle="tooltip"]\').tooltip()');
