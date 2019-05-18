<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-25
 */

namespace app\components\stats\diffs;


use app\components\stats\base\BaseDiff;
use app\components\traits\SetAccountTrait;
use app\models\AccountStats;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class AccountDiff
 *
 * @package app\components\stats\datasets
 */
class AccountDiff extends BaseDiff
{
    use SetAccountTrait;

    public function init()
    {
        parent::init();
        $this->throwExceptionIfFromToAreNotSet();
        $this->throwExceptionIfStatsAttributesIsNotSet();
        $this->throwExceptionIfAccountIsNotSet();
    }

    protected function prepareData(): array
    {
        $data = [];

        $dbTo = $this->to->copy()->endOfDay()->toDateTimeString();
        $dbFrom = $this->from->copy()->endOfDay()->toDateTimeString();

        $toModel = $this->findDataModel($dbTo);

        if (strtotime($toModel['created_at']) > strtotime($dbFrom)) {
            $fromModel = $this->findDataModel($dbFrom, $toModel['id']);
        } else {
            $fromModel = $toModel;
        }

        foreach ($this->statsAttributes as $statsAttribute) {
            $value = ArrayHelper::getValue($toModel, $statsAttribute, 0) - ArrayHelper::getValue($fromModel, $statsAttribute, 0);
            $data[$statsAttribute] = $value;
        }

        return $data;
    }

    protected function findDataModel(string $date, $ignoredStatsId = null)
    {
        $columns = array_map(function ($attr) {
            return "account_stats.{$attr}";
        }, $this->statsAttributes);
        $columns[] = 'id';
        $columns[] = new Expression('DATE(created_at) as created_at');

        return AccountStats::find()
            ->cache()
            ->select($columns)
            ->andWhere(['account_id' => $this->account->id])
            ->andWhere(['<=', 'created_at', $date])
            ->andFilterWhere(['not', ['id' => $ignoredStatsId]])
            ->orderBy('id DESC')
            ->limit(1)
            ->asArray()
            ->one();
    }
}