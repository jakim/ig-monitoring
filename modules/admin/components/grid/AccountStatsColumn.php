<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\components\grid;


use yii\db\Expression;
use yii\grid\DataColumn;

class AccountStatsColumn extends DataColumn
{
    public $format = 'html';

    public $statsAttribute;

    /**
     * @param \app\modules\admin\models\Account $model
     * @param mixed $key
     * @param int $index
     * @return string|void
     */
    public function getDataCellValue($model, $key, $index)
    {
        $attribute = $this->statsAttribute;

        $stats = $model->getAccountStats()
            ->select($attribute)
            ->andWhere(new Expression('account_stats.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)'))
            ->orderBy('account_stats.id DESC')
            ->column();

        if (!$stats) {
            return null;
        }

        $current = $stats['0'];

        if (count($stats) >= 2) {
            $dailyChange = $current - $stats['1'];
        } else {
            $dailyChange = 0;
        }

        if (count($stats) >= 2) {
            $monthlyChange = $current - end($stats);
        } else {
            $monthlyChange = 0;
        }

        return sprintf(
            "%s (%s/%s)",
            $this->grid->formatter->asInteger($model->{$this->attribute}),
            $this->formatChange($dailyChange),
            $this->formatChange($monthlyChange)
        );
    }

    protected function formatChange($number)
    {
        if ($number == 0) {
            return $this->grid->formatter->asInteger($number);
        }

        return sprintf(($number > 0 ? '<span class="text-success">+%s</span>' : '<span class="text-danger">%s</span>'), $this->grid->formatter->asInteger($number));
    }
}