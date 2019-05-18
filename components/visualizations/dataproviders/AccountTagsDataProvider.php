<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-26
 */

namespace app\components\visualizations\dataproviders;


use app\components\stats\providers\AccountTagsDataProvider as BaseAccountTagsDataProvider;
use app\components\visualizations\contracts\DataProviderInterface;
use app\components\visualizations\traits\AccountDataProviderTrait;
use app\dictionaries\Color;
use yii\helpers\ArrayHelper;

class AccountTagsDataProvider extends BaseAccountTagsDataProvider implements DataProviderInterface
{
    use AccountDataProviderTrait;

    public function init()
    {
        $this->statsAttributes = [
            'occurs',
            'ts_avg_likes',
        ];
        parent::init();
        $this->colors['ts_avg_likes'] = Color::INFO;
        $this->throwExceptionIfStatsAttributesIsNotSet();
    }

    protected function prepareLabels()
    {
        return ArrayHelper::getColumn($this->getModels(), 'name');
    }

    protected function prepareDataSets()
    {
        $arr = [];
        foreach ($this->statsAttributes as $attribute) {

            $color = ArrayHelper::getValue($this->colors, $attribute, Color::PRIMARY);

            $data = ArrayHelper::getColumn($this->getModels(), $attribute);

            $arr[] = ArrayHelper::merge([
                'label' => $this->account->getAttributeLabel($attribute),
                'xAxisID' => $attribute,
                'yAxisID' => 'y1',
                'fill' => false,
                'data' => array_values($data),
                'backgroundColor' => $color,
                'borderColor' => $color,
            ], ArrayHelper::getValue($this->dataSetsConfig, $attribute, []));
        }

        return $arr;
    }

    protected function prepareScales()
    {
        return [
            'yAxes' => [
                [
                    'id' => 'y1',
                    'gridLines' => [
                        'display' => true,
                    ],
                ],
            ],
            'xAxes' => [
                [
                    'id' => 'occurs',
//                    'ticks' => [
//                        'min' => 0,
//                        'max' => 30,
//                    ],
                    'display' => false,
                ],
                [
                    'id' => 'ts_avg_likes',
                    'type' => 'linear',
                    'position' => 'bottom',
//                    'ticks' => [
//                        'min' => 0,
//                        'step' => 50,
//                        'max' => 600,
//                    ],
                    'display' => false,
                ],
            ],
        ];
    }
}