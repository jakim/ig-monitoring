<?php

use app\components\visualizations\DateHelper;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $routes array */
/* @var $dateRange string */

$dateRange = DateHelper::getRangeFromUrl();

?>
<div class="row">
    <div class="col-lg-12">
        <div class="pull-left">
            <?php $form = ActiveForm::begin([
                'id' => 'date-range',
                'action' => Url::current(['date_range' => null]),
                'method' => 'get',
            ]) ?>
            <?= DateRangePicker::widget([
                'name' => 'date_range',
                'value' => $dateRange,
                'hideInput' => true,
                'convertFormat' => true,
                'pluginOptions' => [
                    'locale' => [
                        'format' => 'd.m.Y',
                    ],
                    'ranges' => [
                        'Last 7 Days' => ["moment().startOf('day').subtract(6, 'days')", "moment().endOf('day')"],
                        'Last 30 Days' => ["moment().startOf('day').subtract(29, 'days')", "moment().endOf('day')"],
                        'This Month' => ["moment().startOf('month')", "moment().endOf('month')"],
                        'Last Month' => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                        'This Year' => ["moment().startOf('year')", "moment().endOf('year')"],
                        'Last Year' => ["moment().subtract(1, 'year').startOf('year')", "moment().subtract(1, 'year').endOf('year')"],
                    ],
                ],
                'pluginEvents' => [
                    'apply.daterangepicker' => 'function(){jQuery(\'#date-range\').submit();}',
                ],
            ]) ?>
            <?php ActiveForm::end() ?>
        </div>

        <p class="btn-group btn-group-sm pull-right">
            <?php if (isset($routes['table'])): ?>
                <?= Html::a('<span class="fa fa-table"></span>', [$routes['table'], 'id' => $model->id], [
                    'class' => 'btn btn-default ' . ($this->context->action->id == StringHelper::basename($routes['table']) ? 'text-blue active' : ''),
                ]) ?>
            <?php endif; ?>

            <?php if (isset($routes['download'])): ?>
                <?= Html::a('<span class="fa fa-download"></span>', ArrayHelper::merge(Yii::$app->request->get(), (array)$routes['download'], ['id' => $model->id]), [
                    'class' => 'btn btn-default',
                ]) ?>
            <?php endif; ?>
        </p>
    </div>
</div>
