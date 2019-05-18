<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\components\grid;


use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;

class StatsColumn extends DataColumn
{
    public $format = 'html';
    public $numberFormat = 'integer';
    public $statsAttribute;
    public $headerOptions = ['class' => 'sort-numerical'];
    public $visibleData = true;

    /**
     * @var \app\components\stats\diffs\MultiAccountsDiff
     */
    public $dailyDiff;

    /**
     * @var \app\components\stats\diffs\MultiAccountsDiff
     */
    public $monthlyDiff;

    public function init()
    {
        parent::init();
        if (!$this->statsAttribute) {
            $this->statsAttribute = $this->attribute;
        }
    }

    /**
     * @param \app\modules\admin\models\Account|\app\modules\admin\models\Tag $model
     * @param mixed $key
     * @param int $index
     * @return null|string
     */
    public function getDataCellValue($model, $key, $index)
    {
        /** @var \app\components\Formatter $formatter */
        $formatter = $this->grid->formatter;

        $value = [];
        $value[] = $formatter->format($model->{$this->attribute}, $this->numberFormat);

        // daily change
        $key = "{$this->attribute}_daily";
        if ($this->visibleData === true || isset($this->visibleData[$key])) {
            $dailyChange = $this->dailyDiff->getModel($model->id);
            $dailyChange = ArrayHelper::getValue($dailyChange, $this->statsAttribute);
            $value[] = is_numeric($dailyChange) ? $formatter->asChange($dailyChange, true, $this->numberFormat) : '-';
        }

        // monthly change
        $key = "{$this->attribute}_monthly";
        if ($this->visibleData === true || isset($this->visibleData[$key])) {
            $monthlyChange = $this->monthlyDiff->getModel($model->id);
            $monthlyChange = ArrayHelper::getValue($monthlyChange, $this->statsAttribute);
            $value[] = is_numeric($monthlyChange) ? $formatter->asChange($monthlyChange, true, $this->numberFormat) : '-';
        }

        $count = count($value);
        if ($count == 3) {
            return sprintf('%s (%s/%s)', $value['0'], $value['1'], $value['2']);
        } elseif ($count == 2) {
            return sprintf('%s (%s)', $value['0'], $value['1']);
        }

        return $value['0'];
    }
}