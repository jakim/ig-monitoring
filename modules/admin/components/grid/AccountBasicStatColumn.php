<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 16.08.2018
 */

namespace app\modules\admin\components\grid;


use yii\grid\DataColumn;

class AccountBasicStatColumn extends DataColumn
{
    public $format = 'raw';
    public $dataFormat = 'integer';

    public function getDataCellValue($model, $key, $index)
    {
        if ($model->{$this->attribute}) {
            return sprintf('<span data-toggle="tooltip" data-placement="top" title="updated at: %s">%s</span>',
                $this->grid->formatter->asDate($model->stats_updated_at),
                $this->grid->formatter->format($model->{$this->attribute}, $this->dataFormat)
            );
        }

        return '';
    }
}