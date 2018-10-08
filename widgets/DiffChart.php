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

class DiffChart extends Widget
{
    public $options = [
        'height' => 100,
    ];

    public $changes = [];

    public function run()
    {
        echo ChartJs::widget([
            'type' => 'bar',
            'options' => $this->options,
            'clientOptions' => [
                'responsive' => true,
                'legend' => false,
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

        return array_map([$formatter, 'asDate'], array_keys($this->changes));
    }

    protected function datasets()
    {
        $model = new AccountStats();

        $data = ArrayHelper::getColumn($this->changes, 'followed_by');
        $data = array_values($data);

        $colors = [];
        foreach ($data as $key => $value) {
            $colors[] = $data[$key] <= 0 ? '#ff6384' : '#3c8dbc';
        }

        return [
            [
                'label' => $model->getAttributeLabel('followed_by'),
                'yAxisID' => 'followed_by',
                'data' => $data,
                'fill' => false,
                'backgroundColor' => $colors,
                'borderColor' => $colors,
            ],
        ];
    }

    protected function yAxes()
    {
        $ticksStocks = new JsExpression('function(value, index, values) {if (Math.floor(value) === value) {return value;}}');

        return [
            [
                'id' => 'followed_by',
                'type' => 'linear',
                'position' => 'left',
                'ticks' => [
                    'callback' => $ticksStocks,
                ],
            ],
        ];
    }
}