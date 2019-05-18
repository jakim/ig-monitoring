<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\components\grid;


use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;

/**
 * Class OldStatsColumn
 *
 * @package app\modules\admin\components\grid
 *
 * @deprecated
 */
class OldStatsColumn extends DataColumn
{
    public $format = 'html';
    public $numberFormat = 'integer';
    public $statsAttribute;
    public $headerOptions = ['class' => 'sort-numerical'];

    /**
     * @var \app\components\stats\AccountDailyDiff
     */
    public $dailyDiff;

    /**
     * @var \app\components\stats\AccountMonthlyDiff
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

        $lastChange = $this->dailyDiff->getLastDiff($model->id);
        $lastChange = current($lastChange);
        $lastChange = ArrayHelper::getValue($lastChange, $this->statsAttribute);

        $monthlyChanges = $this->monthlyDiff->getLastDiff($model->id);
        $monthlyChange = current($monthlyChanges);
        $monthlyChange = ArrayHelper::getValue($monthlyChange, $this->statsAttribute);

        return sprintf(
            "%s (%s/%s)",
            $formatter->format($model->{$this->attribute}, $this->numberFormat),
            is_numeric($lastChange) ? $formatter->asChange($lastChange, true, $this->numberFormat) : '-',
            is_numeric($monthlyChange) ? $formatter->asChange($monthlyChange, true, $this->numberFormat) : '-'
        );
    }
}