<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-02-13
 */

namespace app\components\traits;


use yii\db\ActiveRecord;
use yii\db\Expression;

trait NextUpdateCalculator
{
    /**
     * If true, then will be automatically calculate from invalidation_count.
     *
     * @param \app\models\Account|\app\models\Tag|ActiveRecord $model
     * @param int $interval
     * @return null|\yii\db\Expression
     */
    protected function getNextUpdateDate(ActiveRecord $model, $interval = null)
    {
        if ($interval === true) { // auto calculate
            $interval = 1;
            for ($i = 1; $i <= (int)$model->invalidation_count; $i++) {
                $interval *= $i;
            }
        }

        if (is_integer($interval)) {
            return new Expression('DATE_ADD(NOW(), INTERVAL :interval HOUR)', [
                'interval' => $interval,
            ]);
        }

        return $interval;
    }
}