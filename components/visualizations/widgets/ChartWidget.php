<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2018-10-28
 */

namespace app\components\visualizations\widgets;


use app\dictionaries\ChartType;
use Carbon\Carbon;
use dosamigos\chartjs\ChartJs;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ChartWidget extends Widget
{
    public $icon = 'fa fa-bar-chart';
    public $title = 'Chart';
    public $type = ChartType::LINE;
    public $aspectRatio = 2;

    public $clientOptions = [];

    /**
     * @var \app\components\visualizations\dataproviders\AccountTrendsDataProvider|array
     */
    public $dataProvider;

    /**
     * @var Carbon
     */
    public $from;

    /**
     * @var Carbon
     */
    public $to;

    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;

    public $labelFormat = 'date';

    /**
     * @var \app\components\Formatter
     */
    protected $formatter;

    public function init()
    {
        parent::init();
        $this->setId(sprintf('%s_chart', $this->getId()));

        $this->dataProvider = Yii::createObject(ArrayHelper::merge($this->dataProvider, [
            'account' => $this->model,
            'from' => $this->from,
            'to' => $this->to,
        ]));
        $this->formatter = Yii::$app->formatter;
    }

    public function run()
    {
        $config = [
            'id' => $this->id,
            'type' => $this->type,
            'clientOptions' => ArrayHelper::merge([
                'responsive' => true,
                'aspectRatio' => $this->aspectRatio,
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltips' => [
                    'mode' => 'index',
                    'position' => 'nearest',
                ],
                'scales' => $this->dataProvider->scales(),
            ], $this->clientOptions),
            'data' => [
                'labels' => $this->dataProvider->labels(),
                'datasets' => $this->dataProvider->dataSets(),
            ],
        ];

        echo '<div class="box" id="' . $this->getId() . '_box">';
        $this->renderHeader();
        echo '<div class="box-body">';
        echo ChartJs::widget($config);
        echo '</div>';
        echo '</div>';
    }

    protected function renderHeader()
    {
        echo '<div class="box-header with-border">';
        echo $this->icon ? "<span class='$this->icon'></span>" : '';
        echo $this->title ? sprintf('<h3 class="box-title">%s <small>%s - %s</small></h3>',
            Html::encode($this->title),
            $this->formatter->asDate($this->dataProvider->getFrom()->timestamp),
            $this->formatter->asDate($this->dataProvider->getTo()->timestamp)
        ) : '';
        echo '</div>';
    }
}