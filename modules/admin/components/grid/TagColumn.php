<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 16.08.2018
 */

namespace app\modules\admin\components\grid;


use app\dictionaries\TagInvalidationType;
use jakim\ig\Url;
use yii\grid\DataColumn;
use yii\helpers\Html;

class TagColumn extends DataColumn
{
    public $format = 'raw';
    public $displayDashboardLink = false;

    public function getDataCellValue($model, $key, $index)
    {
        $html = [];
        if ($this->displayDashboardLink || $model->monitoring) {
            $html[] = Html::a($model->namePrefixed, ['tag/stats', 'id' => $model->id]);
        } else {
            $html[] = $model->{$this->attribute};
        }
        $html[] = Html::a('<span class="fa fa-external-link text-sm"></span>', Url::tag($model->name), ['target' => '_blank']);

        if (!$model->is_valid) {
            $html[] = sprintf(
                '<span class="fa fa-exclamation-triangle text-danger pull-right" data-toggle="tooltip" data-placement="top"  title="%s, attempts: %s"></span>',
                TagInvalidationType::getLabel($model->invalidation_type_id, 'Unknown reason'),
                $model->invalidation_count
            );
        }
        if ($model->disabled) {
            $html[] = '<span class="fa fa-exclamation-triangle text-danger pull-right" title="Not found."></span>';
        }

        return implode(" \n", $html);
    }
}