<?php

use app\components\stats\diffs\AccountDiff;
use app\components\visualizations\dataproviders\AccountChangesDataProvider;
use app\components\visualizations\dataproviders\AccountTrendsDataProvider;
use app\components\visualizations\widgets\ChangeRowWidget;
use app\components\visualizations\widgets\ChartWidget;
use app\dictionaries\ChartType;
use app\dictionaries\Grouping;
use app\modules\admin\widgets\NoStatsDataAlert;
use Carbon\Carbon;

/**
 * @var $this yii\web\View
 * @var $model app\models\Account
 * @var array $lastDailyChange
 * @var array $dailyChanges
 * @var array $lastMonthlyChange
 * @var array $monthlyChanges
 * @var array $dailyStats
 */

$this->title = "{$model->usernamePrefixed} :: Dashboard";
$this->params['breadcrumbs'][] = ['label' => 'Monitoring', 'url' => ['monitoring/accounts']];
$this->params['breadcrumbs'][] = ['label' => $model->usernamePrefixed, 'url' => ['dashboard', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Dashboard';

/** @var \app\components\Formatter $formatter */
$formatter = Yii::$app->formatter;


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

                    <?= NoStatsDataAlert::widget(['model' => $model]) ?>

                    <?= ChangeRowWidget::widget([
                        'account' => $model,
                        'statsAttributes' => [
                            'er' => ['percent', 2],
                            'followed_by' => 'integer',
                            'follows' => 'integer',
                            'media' => 'integer',
                        ],
                        'header' => 'Daily change',
                        'diff' => [
                            'class' => AccountDiff::class,
                            'from' => Carbon::now()->subDays(2),
                            'to' => Carbon::yesterday(),
                        ],
                    ]) ?>

                    <?= ChangeRowWidget::widget([
                        'account' => $model,
                        'statsAttributes' => [
                            'er' => ['percent', 2],
                            'followed_by' => 'integer',
                            'follows' => 'integer',
                            'media' => 'integer',
                        ],
                        'header' => 'Monthly change',
                        'diff' => [
                            'class' => AccountDiff::class,
                            'from' => Carbon::yesterday()->subMonth()->endOfMonth(),
                            'to' => Carbon::yesterday(),
                        ],
                    ]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <?= ChartWidget::widget([
                        'model' => $model,
                        'aspectRatio' => 3,
                        'type' => ChartType::LINE,
                        'icon' => 'fa fa-line-chart',
                        'title' => 'Trends',
                        'dataProvider' => [
                            'class' => AccountTrendsDataProvider::class,
                        ],
                        'from' => Carbon::now()->subMonth(),
                        'to' => Carbon::yesterday(),
                    ])
                    ?>

                    <?=
                    ChartWidget::widget([
                        'model' => $model,
                        'aspectRatio' => 3,
                        'type' => ChartType::BAR,
                        'title' => 'Daily change in the number of followers',
                        'clientOptions' => [
                            'legend' => false,
                        ],
                        'dataProvider' => [
                            'class' => AccountChangesDataProvider::class,
                        ],
                        'from' => Carbon::now()->subMonth(),
                        'to' => Carbon::yesterday(),
                    ])
                    ?>

                    <?=
                    ChartWidget::widget([
                        'model' => $model,
                        'aspectRatio' => 3,
                        'type' => ChartType::BAR,
                        'title' => 'Monthly change in the number of followers',
                        'clientOptions' => [
                            'legend' => false,
                        ],
                        'dataProvider' => [
                            'class' => AccountChangesDataProvider::class,
                            'grouping' => Grouping::MONTHLY,
                        ],
                        'from' => Carbon::now()->subYear(),
                        'to' => Carbon::yesterday(),
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
