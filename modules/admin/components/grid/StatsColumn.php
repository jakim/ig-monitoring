<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\components\grid;


use yii\grid\DataColumn;

class StatsColumn extends DataColumn
{
    public $format = 'html';
    public $numberFormat = 'integer';
    public $statsAttribute;
    public $headerOptions = ['class' => 'sort-numerical'];

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
        if (!$model->lastStats) {
            return null;
        }

        /** @var \app\components\Formatter $formatter */
        $formatter = $this->grid->formatter;

        $lastChange = $model->lastChange($this->statsAttribute);
        $monthlyChange = $model->monthlyChange($this->statsAttribute);

        return sprintf(
            "%s (%s/%s)",
            $formatter->format($model->lastStats->{$this->statsAttribute}, $this->numberFormat),
            $lastChange ? $formatter->asChange($lastChange, true, $this->numberFormat) : $lastChange,
            $monthlyChange ? $formatter->asChange($monthlyChange, true, $this->numberFormat) : $monthlyChange
        );
    }
}