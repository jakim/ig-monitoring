<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.06.2018
 */

namespace app\widgets;


use app\models\AccountStats;
use dosamigos\chartjs\ChartJs;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

class ProgressChart extends Widget
{
    public $options = [
        'height' => 200,
    ];

    public $attributes = [
        'er',
        'followed_by',
        'follows',
        'media',
    ];

    public $colors = [
        '#00a65a',
        '#3c8dbc',
        '#605ca8',
        '#ff851b',
    ];

    public $stats = [];

    protected function yAxes()
    {
        $ticksStocks = new JsExpression('function(value, index, values) {if (Math.floor(value) === value) {return value;}}');
        $arr = [];
        foreach ($this->attributes as $attribute) {
            $arr[] = [
                'id' => $attribute,
                'type' => 'linear',
                'position' => 'right',
                'ticks' => [
                    'callback' => $ticksStocks,
                ],
            ];
        }

        return $arr;
    }

    protected function labels()
    {
        $formatter = \Yii::$app->formatter;

        return array_map([$formatter, 'asDate'], array_keys($this->stats));
    }

    protected function datasets()
    {
        $stats = array_values($this->stats);
        $model = new AccountStats();
        $arr = [];
        foreach ($this->attributes as $attribute) {
            $color = array_shift($this->colors);
            $data = ArrayHelper::getColumn($stats, $attribute);
            if ($attribute == 'er') {
                $data = array_map(function ($item) {
                    return number_format($item * 100, 2);
                }, $data);
            }
            $arr[] = [
                'label' => $model->getAttributeLabel($attribute),
                'yAxisID' => $attribute,
                'data' => $data,
                'fill' => false,
                'backgroundColor' => $color,
                'borderColor' => $color,
            ];
        }

        return $arr;
    }

    public function run()
    {

        return ChartJs::widget([
            'type' => 'line',
            'options' => $this->options,
            'clientOptions' => [
                'responsive' => true,
                'tooltips' => [
                    'mode' => 'index',
                    'position' => 'nearest',
                ],
                'scales' => [
                    'yAxes' => $this->yAxes(),
                ],
            ],
            'data' => [
                'labels' => $this->labels(),
                'datasets' => $this->datasets(),
            ],
        ]);
    }
}