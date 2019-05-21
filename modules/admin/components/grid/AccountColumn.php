<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 16.08.2018
 */

namespace app\modules\admin\components\grid;


use app\dictionaries\AccountInvalidationType;
use app\modules\admin\models\Account;
use jakim\ig\Url;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class AccountColumn extends DataColumn
{
    public $format = 'raw';
    public $displayDashboardLink = false;

    /**
     * @param Account $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function getDataCellValue($model, $key, $index)
    {
        $html = [];

        if ($this->displayDashboardLink || $model->monitoring) {
            $html[] = Html::a(ArrayHelper::getValue($model, 'displayName') ?: $model->usernamePrefixed, ['account/dashboard', 'id' => $model->id]);
        } else {
            $html[] = $model->{$this->attribute};
        }

        if ($model->is_verified) {
            $html[] = ' <i class="fa fa-check-circle text-sm text-blue" data-toggle="tooltip" data-placement="top"  title="Verified"></i>';
        }
        if ($model->is_business) {
            $html[] = sprintf(' <i class="fa fa-briefcase text-sm text-muted" data-toggle="tooltip" data-placement="top"  title="%s"></i>', Html::encode($model->business_category));
        }
        $html[] = Html::a(' <span class="fa fa-external-link text-sm fa-fw"></span>', Url::account($model->username), ['target' => '_blank']);

        if (!$model->is_valid) {
            $html[] = sprintf(
                '<span class="fa fa-exclamation-triangle text-danger pull-right" data-toggle="tooltip" data-placement="top"  title="%s, attempts: %s"></span>',
                AccountInvalidationType::getLabel($model->invalidation_type_id, 'Unknown reason'),
                $model->invalidation_count
            );
        }

        return implode(" \n", $html);
    }
}