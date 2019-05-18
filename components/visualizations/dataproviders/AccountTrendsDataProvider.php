<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-26
 */

namespace app\components\visualizations\dataproviders;


use app\components\stats\providers\AccountDataProvider;
use app\components\visualizations\contracts\DataProviderInterface;
use app\components\visualizations\traits\AccountDataProviderTrait;
use app\dictionaries\Color;
use yii\helpers\ArrayHelper;

class AccountTrendsDataProvider extends AccountDataProvider implements DataProviderInterface
{
    use AccountDataProviderTrait;

    public function init()
    {
        if (empty($this->statsAttributes)) {
            $this->statsAttributes = [
                'followed_by',
                'follows',
                'media',
                'er',
                'avg_likes',
                'avg_comments',
            ];
        }

        parent::init();

        $this->dataSetsConfig = [
            'avg_likes' => [
                'hidden' => true,
            ],
            'avg_comments' => [
                'hidden' => true,
            ],
        ];

        $this->scalesConfig = [
            'followed_by' => ['display' => false],
            'er' => ['display' => false],
            'follows' => ['display' => false],
            'media' => ['display' => false],
            'avg_likes' => ['display' => false],
            'avg_comments' => ['display' => false],
        ];
    }

    protected function prepareLabels()
    {
        $formatter = \Yii::$app->formatter;

        return array_map(function ($label) use ($formatter) {
            return $formatter->format($label, $this->labelFormat);
        }, $this->getKeys());
    }

    protected function prepareDataSets()
    {
        $arr = [];
        foreach ($this->statsAttributes as $attribute) {

            $color = ArrayHelper::getValue($this->colors, $attribute, Color::PRIMARY);

            $data = ArrayHelper::getColumn($this->getModels(), $attribute);
            if ($attribute == 'er') {
                $data = array_map(function ($item) {
                    return number_format($item * 100, 2);
                }, $data);
            }

            $arr[] = ArrayHelper::merge([
                'label' => $this->account->getAttributeLabel($attribute),
                'yAxisID' => $attribute,
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
        $arr = [];
        foreach ($this->statsAttributes as $attribute) {
            $arr[] = ArrayHelper::merge([
                'id' => $attribute,
                'type' => 'linear',
                'position' => 'left',
            ], ArrayHelper::getValue($this->scalesConfig, $attribute, []));
        }

        return ['yAxes' => $arr];
    }
}