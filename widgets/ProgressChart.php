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
        'media',
        'follows',
        'followed_by',
        'er',
        'avg_likes',
        'avg_comments',
    ];

    public $colors = [
        '#ff851b',
        '#605ca8',
        '#3c8dbc',
        '#00a65a',
        '#39CCCC',
        '#D81B60',
    ];

    public $stats = [];

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

    protected function yAxes()
    {
        $ticksStocks = new JsExpression('function(value, index, values) {if (Math.floor(value) === value) {return value;}}');
        $attributes = array_chunk($this->attributes, 3);
        $arr = [];
        $this->prepareYAxes(array_reverse($attributes['0']), $ticksStocks, $arr, 'left');
        $this->prepareYAxes($attributes['1'], $ticksStocks, $arr, 'right');

        return $arr;
    }

    private function prepareYAxes($attributes, $ticksStocks, &$arr, $position = 'left')
    {
        foreach ($attributes as $attribute) {
            $arr[] = [
                'id' => $attribute,
                'display' => false,
                'type' => 'linear',
                'position' => $position,
                'ticks' => [
                    'callback' => $ticksStocks,
                ],
            ];
        }
    }
}