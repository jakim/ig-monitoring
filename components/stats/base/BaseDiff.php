<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-25
 */

namespace app\components\stats\base;


use app\components\stats\contracts\DiffInterface;
use app\components\stats\traits\FromToDateTrait;
use app\components\stats\traits\StatsAttributesTrait;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

abstract class BaseDiff extends BaseObject implements DiffInterface
{
    use FromToDateTrait, StatsAttributesTrait;

    protected $data;

    public function getModel($key): array
    {
        return ArrayHelper::getValue($this->getData(), $key, []);
    }

    public function getData()
    {
        if ($this->data === null) {
            $this->data = $this->prepareData();
        }

        return $this->data;
    }

    abstract protected function prepareData(): array;
}