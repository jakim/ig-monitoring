<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-26
 */

namespace app\components\visualizations\dataproviders;


use app\components\stats\providers\AccountDiffDataProvider;
use app\components\visualizations\contracts\DataProviderInterface;
use app\components\visualizations\traits\AccountDataProviderTrait;
use app\dictionaries\Color;
use Yii;
use yii\helpers\ArrayHelper;

class AccountChangesDataProvider extends AccountDiffDataProvider implements DataProviderInterface
{
    use AccountDataProviderTrait;

    public function init()
    {
        $this->statsAttributes = [
            'followed_by',
        ];

        parent::init();
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

    protected function prepareLabels()
    {
        $formatter = Yii::$app->formatter;

        return array_map(function ($label) use ($formatter) {
            return $formatter->format($label, $this->labelFormat);
        }, $this->getKeys());
    }

    protected function prepareDataSets()
    {
        $arr = [];
        foreach ($this->statsAttributes as $attribute) {

            $data = ArrayHelper::getColumn($this->getModels(), $attribute);

            $colors = [];
            foreach ($data as $value) {
                $colors[] = $value >= 0 ? ArrayHelper::getValue($this->colors, $attribute, Color::PRIMARY) : Color::DANGER;
            }

            $arr[] = ArrayHelper::merge([
                'label' => $this->account->getAttributeLabel($attribute),
                'yAxisID' => $attribute,
                'fill' => false,
                'data' => array_values($data),
                'backgroundColor' => $colors,
                'borderColor' => $colors,
            ], ArrayHelper::getValue($this->dataSetsConfig, $attribute, []));
        }

        return $arr;
    }
}