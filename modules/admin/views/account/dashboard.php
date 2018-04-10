<?php

use app\modules\admin\widgets\ChangeInfoBox;
use dosamigos\chartjs\ChartJs;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Account */
/* @var $manager \app\modules\admin\components\AccountStatsManager */

$this->title = "{$model->usernamePrefixed} :: Dashboard";
$this->params['breadcrumbs'][] = ['label' => 'Monitoring', 'url' => ['monitoring/accounts']];
$this->params['breadcrumbs'][] = ['label' => $model->usernamePrefixed, 'url' => ['dashboard', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Dashboard';

/** @var \app\components\Formatter $formatter */
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
                    <?php if (!$manager->lastChange('followed_by')): ?>
                        <div class="callout callout-info">
                            <p class="lead"><span class="fa fa-cog fa-spin"></span> Collecting data...</p>
                            <p>Please come back tomorrow.</p>
                        </div>
                    <?php endif; ?>

                    <?php if ($manager->lastChange('followed_by')): ?>
                        <h2 class="page-header">
                            Daily change
                            <small class="pull-right">
                                since: <?= $formatter->asDate($manager->lastStatsFrom()) ?></small>
                        </h2>
                        <div class="row">
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('er'),
                                    'number' => $manager->lastChange('er'),
                                    'format' => ['percent', 2],
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('followed_by'),
                                    'number' => $manager->lastChange('followed_by'),
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('follows'),
                                    'number' => $manager->lastChange('follows'),
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('media'),
                                    'number' => $manager->lastChange('media'),
                                ]) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($manager->lastMonthChange('followed_by')): ?>
                        <h2 class="page-header">
                            Monthly change
                            <small class="pull-right">
                                since: <?= $formatter->asDate($manager->dailyStatsFrom()) ?></small>
                        </h2>
                        <div class="row">
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('er'),
                                    'number' => $manager->lastMonthChange('er'),
                                    'format' => ['percent', 2],
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('followed_by'),
                                    'number' => $manager->lastMonthChange('followed_by'),
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('follows'),
                                    'number' => $manager->lastMonthChange('follows'),
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('media'),
                                    'number' => $manager->lastMonthChange('media'),
                                ]) ?>
                            </div>
                        </div>

                        <h2 class="page-header">
                            <?= sprintf('Stats from %s to %s',
                                $formatter->asDate($manager->dailyStatsFrom()),
                                $formatter->asDate($model->lastAccountStats->created_at)
                            ); ?>
                        </h2>

                        <?php
                        $dailyStatsData = $manager->getDailyStatsData(false);
                        $ticksStocks = new JsExpression('function(value, index, values) {if (Math.floor(value) === value) {return value;}}');

                        echo ChartJs::widget([
                            'type' => 'line',
                            'options' => [
                                'height' => 200,
                            ],
                            'clientOptions' => [
                                'responsive' => true,
                                'tooltips' => [
                                    'mode' => 'index',
                                    'position' => 'nearest',
                                ],
                                'scales' => [
                                    'yAxes' => [
                                        [
                                            'id' => 'er',
                                            'type' => 'linear',
                                            'position' => 'right',
                                            'ticks' => [
                                                'callback' => $ticksStocks,
                                            ],
                                        ],
                                        [
                                            'id' => 'followed_by',
                                            'type' => 'linear',
                                            'position' => 'right',
                                            'ticks' => [
                                                'callback' => $ticksStocks,
                                            ],
                                        ],
                                        [
                                            'id' => 'follows',
                                            'type' => 'linear',
                                            'position' => 'right',
                                            'ticks' => [
                                                'callback' => $ticksStocks,
                                            ],
                                        ],
                                        [
                                            'id' => 'media',
                                            'type' => 'linear',
                                            'position' => 'right',
                                            'ticks' => [
                                                'callback' => $ticksStocks,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'data' => [
                                'labels' => array_map([$formatter, 'asDate'], ArrayHelper::getColumn($dailyStatsData, 'created_at')),
                                'datasets' => [
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('er'),
                                        'yAxisID' => 'er',
                                        'data' => array_map(function($item) {
                                            return number_format($item * 100, 2);
                                        }, ArrayHelper::getColumn($dailyStatsData, 'er')),
                                        'fill' => false,
                                        'backgroundColor' => '#00a65a',
                                        'borderColor' => '#00a65a',
                                    ],
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('followed_by'),
                                        'yAxisID' => 'followed_by',
                                        'data' => ArrayHelper::getColumn($dailyStatsData, 'followed_by'),
                                        'fill' => false,
                                        'backgroundColor' => '#3c8dbc',
                                        'borderColor' => '#3c8dbc',
                                    ],
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('follows'),
                                        'yAxisID' => 'follows',
                                        'data' => ArrayHelper::getColumn($dailyStatsData, 'follows'),
                                        'fill' => false,
                                        'backgroundColor' => '#605ca8',
                                        'borderColor' => '#605ca8',
                                    ],
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('media'),
                                        'yAxisID' => 'media',
                                        'data' => ArrayHelper::getColumn($dailyStatsData, 'media'),
                                        'fill' => false,
                                        'backgroundColor' => '#ff851b',
                                        'borderColor' => '#ff851b',
                                    ],
                                ],
                            ],
                        ]);
                        ?>

                        <br>
                        <h2 class="page-header">
                            Followed by, change from last 30 days
                            <small class="pull-right">
                                since: <?= $formatter->asDate($manager->dailyStatsFrom()) ?></small>
                        </h2>
                        <?php

                        $data = ArrayHelper::getColumn($dailyStatsData, 'followed_by');
                        $prevValue = array_shift($data);
                        $labels = array_map([$formatter, 'asDate'], ArrayHelper::getColumn($dailyStatsData, 'created_at'));
                        array_shift($labels);
                        $colors = [];

                        foreach ($data as $key => $value) {
                            $data[$key] = $value - $prevValue;
                            $colors[] = $data[$key] <= 0 ? '#ff6384' : '#3c8dbc';
                            $prevValue = $value;
                        }

                        echo ChartJs::widget([
                            'type' => 'bar',
                            'options' => [
                                'height' => 100,
                            ],
                            'clientOptions' => [
                                'responsive' => true,
                                'tooltips' => [
                                    'mode' => 'index',
                                    'position' => 'nearest',
                                ],
                                'legend' => [
                                    'display' => false,
                                ],
                                'scales' => [
                                    'yAxes' => [
                                        [
                                            'id' => 'followed_by',
                                            'type' => 'linear',
                                            'position' => 'right',
                                            'ticks' => [
                                                'callback' => $ticksStocks,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'data' => [
                                'labels' => $labels,
                                'datasets' => [
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('followed_by'),
                                        'yAxisID' => 'followed_by',
                                        'data' => $data,
                                        'fill' => false,
                                        'backgroundColor' => $colors,
                                        'borderColor' => $colors,
                                    ],
                                ],
                            ],
                        ]);
                        ?>
                    <?php endif; ?>

                    <?php if (($monthlyStatsData = $manager->getMonthlyStatsData(false))): ?>

                        <br>
                        <h2 class="page-header">
                            Followed by, change from last year
                            <small class="pull-right">
                                since: <?= $formatter->asDate($manager->monthlyStatsFrom()) ?></small>
                        </h2>
                        <?php
                        $data = ArrayHelper::getColumn($monthlyStatsData, 'followed_by');
                        $prevValue = array_shift($data);
                        $labels = array_map([$formatter, 'asDate'], ArrayHelper::getColumn($monthlyStatsData, 'created_at'));
                        array_shift($labels);
                        $colors = [];

                        foreach ($data as $key => $value) {
                            $data[$key] = $value - $prevValue;
                            $colors[] = $data[$key] <= 0 ? '#ff6384' : '#3c8dbc';
                            $prevValue = $value;
                        }

                        echo ChartJs::widget([
                            'type' => 'bar',
                            'options' => [
                                'height' => 100,
                            ],
                            'clientOptions' => [
                                'responsive' => true,
                                'tooltips' => [
                                    'mode' => 'index',
                                    'position' => 'nearest',
                                ],
                                'legend' => [
                                    'display' => false,
                                ],
                                'scales' => [
                                    'yAxes' => [
                                        [
                                            'id' => 'followed_by',
                                            'type' => 'linear',
                                            'position' => 'right',
                                            'ticks' => [
                                                'callback' => $ticksStocks,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'data' => [
                                'labels' => $labels,
                                'datasets' => [
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('followed_by'),
                                        'yAxisID' => 'followed_by',
                                        'data' => $data,
                                        'fill' => false,
                                        'backgroundColor' => $colors,
                                        'borderColor' => $colors,
                                    ],
                                ],
                            ],
                        ]);
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
