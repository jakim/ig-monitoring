<?php

use app\dictionaries\TrackerType;
use app\modules\admin\components\grid\GridView;
use app\modules\admin\components\grid\StatsColumn;
use app\modules\admin\components\grid\TagColumn;
use app\modules\admin\widgets\CreateMonitoringModal;
use yii\grid\SerialColumn;

/**
 * @var yii\web\View $this
 * @var app\modules\admin\models\TagSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $dailyDiff
 * @var array $monthlyDiff
 */

$this->title = 'Monitoring :: Tags';
$this->params['breadcrumbs'][] = 'Monitoring';
$this->params['breadcrumbs'][] = 'Tags';
?>
    <div class="tag-index nav-tabs-custom">

        <?= $this->render('_tabs') ?>

        <div class="tab-content">

            <?php if ($dataProvider->totalCount): ?>
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-lg-12 text-right">
                        <?= CreateMonitoringModal::widget([
                            'trackerType' => TrackerType::TAG,
                            'modalToggleButton' => ['label' => 'Add tags'],
                        ]) ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="table-responsive">

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'emptyText' => sprintf('<p class="lead">This is a screen where you can compare metrics for multiple tags.</p>%s',
                        CreateMonitoringModal::widget([
                            'trackerType' => TrackerType::TAG,
                            'modalToggleButton' => [
                                'label' => 'Add first tag',
                                'class' => 'btn btn-lg btn-success',
                            ],
                        ])),
                    'emptyTextOptions' => [
                        'class' => 'text-center empty',
                    ],
                    'columns' => [
                        ['class' => SerialColumn::class],
                        [
                            'class' => TagColumn::class,
                            'attribute' => 'name',
                            'displayDashboardLink' => true,
                            'visible' => true,
                        ],
                        [
                            'class' => StatsColumn::class,
                            'attribute' => 'media',
                            'dailyDiff' => $dailyDiff,
                            'monthlyDiff' => $monthlyDiff,
                        ],
                        [
                            'class' => StatsColumn::class,
                            'attribute' => 'likes',
                            'dailyDiff' => $dailyDiff,
                            'monthlyDiff' => $monthlyDiff,
                        ],
                        [
                            'class' => StatsColumn::class,
                            'attribute' => 'comments',
                            'dailyDiff' => $dailyDiff,
                            'monthlyDiff' => $monthlyDiff,
                        ],
                        [
                            'class' => StatsColumn::class,
                            'attribute' => 'min_likes',
                            'dailyDiff' => $dailyDiff,
                            'monthlyDiff' => $monthlyDiff,
                        ],
                        [
                            'class' => StatsColumn::class,
                            'attribute' => 'max_likes',
                            'dailyDiff' => $dailyDiff,
                            'monthlyDiff' => $monthlyDiff,
                        ],
                        [
                            'class' => StatsColumn::class,
                            'attribute' => 'min_comments',
                            'dailyDiff' => $dailyDiff,
                            'monthlyDiff' => $monthlyDiff,
                        ],
                        [
                            'class' => StatsColumn::class,
                            'attribute' => 'max_comments',
                            'dailyDiff' => $dailyDiff,
                            'monthlyDiff' => $monthlyDiff,
                        ],
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
