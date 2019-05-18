<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-28
 */

namespace app\components\visualizations\dataproviders;


use app\components\stats\providers\AccountAccountsDataProvider as BaseAccountAccountsDataProvider;
use app\components\visualizations\contracts\DataProviderInterface;
use app\components\visualizations\traits\AccountDataProviderTrait;
use app\dictionaries\Color;
use yii\helpers\ArrayHelper;

class AccountAccountsDataProvider extends BaseAccountAccountsDataProvider implements DataProviderInterface
{
    use AccountDataProviderTrait;

    public function init()
    {
        $this->statsAttributes = [
            'occurs',
        ];
        parent::init();
        $this->throwExceptionIfStatsAttributesIsNotSet();
    }

    protected function prepareLabels(): array
    {
        return ArrayHelper::getColumn($this->getModels(), 'username');
    }

    protected function prepareDataSets(): array
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

    protected function prepareScales(): array
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
                    'id' => 'ts_likes',
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