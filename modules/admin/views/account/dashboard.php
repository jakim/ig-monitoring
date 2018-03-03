<?php

use app\modules\admin\widgets\ChangeInfoBox;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

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
                    <?php if (!$model->beforeLastAccountStats): ?>
                        <div class="callout callout-info">
                            <p class="lead"><span class="fa fa-cog fa-spin"></span> Collecting data...</p>
                            <p>Please come back tomorrow.</p>
                        </div>
                    <?php endif; ?>

                    <?php if ($model->beforeLastAccountStats): ?>
                        <h2 class="page-header">
                            Daily change
                            <small class="pull-right">
                                since: <?= $formatter->asDate($model->beforeLastAccountStats->created_at) ?></small>
                        </h2>
                        <div class="row">
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('er'),
                                    'number' => $model->lastChange('er'),
                                    'format' => ['percent', 2],
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('followed_by'),
                                    'number' => $model->lastChange('followed_by'),
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('follows'),
                                    'number' => $model->lastChange('follows'),
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('media'),
                                    'number' => $model->lastChange('media'),
                                ]) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($model->beforeMonthAccountStats): ?>
                        <h2 class="page-header">
                            Monthly change
                            <small class="pull-right">
                                since: <?= $formatter->asDate($model->beforeMonthAccountStats->created_at) ?></small>
                        </h2>
                        <div class="row">
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('er'),
                                    'number' => $model->monthlyChange('er'),
                                    'format' => ['percent', 2],
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('followed_by'),
                                    'number' => $model->monthlyChange('followed_by'),
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('follows'),
                                    'number' => $model->monthlyChange('follows'),
                                ]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= ChangeInfoBox::widget([
                                    'header' => $model->lastAccountStats->getAttributeLabel('media'),
                                    'number' => $model->monthlyChange('media'),
                                ]) ?>
                            </div>
                        </div>

                        <h2 class="page-header">
                            <?= sprintf('Stats from %s to %s',
                                $formatter->asDate($model->beforeMonthAccountStats->created_at),
                                $formatter->asDate($model->lastAccountStats->created_at)
                            ); ?>
                        </h2>

                        <?php

                        $ticksStocks = new JsExpression('function(value, index, values) {if (Math.floor(value) === value) {return value;}}');
                        $monthAccountStats = array_reverse($model->monthAccountStats);

                        echo \dosamigos\chartjs\ChartJs::widget([
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
                                'labels' => array_map([$formatter, 'asDate'], ArrayHelper::getColumn($monthAccountStats, 'created_at')),
                                'datasets' => [
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('er'),
                                        'yAxisID' => 'er',
                                        'data' => array_map(function ($item) {
                                            return number_format($item * 100, 2);
                                        }, ArrayHelper::getColumn($monthAccountStats, 'er')),
                                        'fill' => false,
                                        'backgroundColor' => '#00a65a',
                                        'borderColor' => '#00a65a',
                                    ],
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('followed_by'),
                                        'yAxisID' => 'followed_by',
                                        'data' => ArrayHelper::getColumn($monthAccountStats, 'followed_by'),
                                        'fill' => false,
                                        'backgroundColor' => '#3c8dbc',
                                        'borderColor' => '#3c8dbc',
                                    ],
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('follows'),
                                        'yAxisID' => 'follows',
                                        'data' => ArrayHelper::getColumn($monthAccountStats, 'follows'),
                                        'fill' => false,
                                        'backgroundColor' => '#605ca8',
                                        'borderColor' => '#605ca8',
                                    ],
                                    [
                                        'label' => $model->lastAccountStats->getAttributeLabel('media'),
                                        'yAxisID' => 'media',
                                        'data' => ArrayHelper::getColumn($monthAccountStats, 'media'),
                                        'fill' => false,
                                        'backgroundColor' => '#ff851b',
                                        'borderColor' => '#ff851b',
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
