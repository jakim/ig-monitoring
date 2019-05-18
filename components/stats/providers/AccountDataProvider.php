<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-24
 */

namespace app\components\stats\providers;


use app\components\stats\contracts\DataProviderInterface;
use app\components\stats\traits\FromToDateTrait;
use app\components\stats\traits\StatsAttributesTrait;
use app\components\traits\SetAccountTrait;
use app\dictionaries\Grouping;
use app\models\AccountStats;
use Carbon\Carbon;
use yii\data\BaseDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class AccountDataProvider
 *
 * @package app\components\stats\providers
 *
 * @property \Carbon\Carbon $from
 * @property \Carbon\Carbon $to
 */
class AccountDataProvider extends BaseDataProvider implements DataProviderInterface
{
    use FromToDateTrait, SetAccountTrait, StatsAttributesTrait;

    public $grouping = Grouping::DAILY;

    public function init()
    {
        parent::init();
        $this->throwExceptionIfFromToAreNotSet();
        $this->throwExceptionIfAccountIsNotSet();
        $this->throwExceptionIfStatsAttributesIsNotSet();
    }

    /**
     * Prepares the data models that will be made available in the current page.
     *
     * @return array the available data models
     */
    protected function prepareModels()
    {
        $models = [];

        $dbFrom = $this->from->copy()->endOfDay();
        $dbFrom = $this->findDbDate($dbFrom);

        $dbTo = $this->to->copy()->endOfDay()->toDateTimeString();

        $idsQuery = $this->findStatsIds($dbFrom, $dbTo, false);
        $data = $this->findDataModels($idsQuery);

        foreach ($data as $stats) {
            foreach ($this->statsAttributes as $statsAttribute) {
                $value = ArrayHelper::getValue($stats, $statsAttribute, 0);
                $models[$stats['created_at']][$statsAttribute] = $value;
            }
        }

        return $models;
    }

    /**
     * Prepares the keys associated with the currently available data models.
     *
     * @param array $models the available data models
     * @return array the keys
     */
    protected function prepareKeys($models)
    {
        return array_keys($models);
    }

    /**
     * Returns a value indicating the total number of data models in this data provider.
     *
     * @return int total number of data models in this data provider.
     */
    protected function prepareTotalCount()
    {
        return count($this->models);
    }

    protected function findDbDate(Carbon $date)
    {
        return AccountStats::find()
            ->select('created_at')
            ->andWhere(['account_id' => $this->account->id])
            ->andWhere(['<=', 'created_at', $date->toDateTimeString()])
            ->orderBy('created_at DESC')
            ->limit(1)
            ->scalar();
    }

    protected function findStatsIds(?string $dbFrom, ?string $dbTo, bool $asQuery = false)
    {
        switch ($this->grouping) {
            case Grouping::MONTHLY:
                $groupBy = new Expression('DATE_FORMAT(created_at, \'%Y-%m\')');
                break;
            case Grouping::WEEKLY:
                $groupBy = new Expression('DATE_FORMAT(created_at, \'%Y-%u\')');
                break;
            case Grouping::DAILY:
            default:
                $groupBy = new Expression('DATE(created_at)');
        }

        $q = AccountStats::find()
            ->select(new Expression('MAX(id) as id'))
            ->andWhere(['account_id' => $this->account->id])
            ->andFilterWhere(['>=', 'created_at', $dbFrom])
            ->andWhere(['<=', 'created_at', $dbTo])
            ->groupBy($groupBy);

        if ($asQuery) {
            return $q;
        }

        return $q->column();
    }

    /**
     * @param $ids
     * @return \app\models\AccountStats[]|array|\yii\db\ActiveRecord[]
     */
    protected function findDataModels($ids)
    {
        $columns = array_map(function ($attr) {
            return "account_stats.{$attr}";
        }, $this->statsAttributes);
        $columns[] = new Expression('DATE(created_at) as created_at');

        return AccountStats::find()
            ->select($columns)
            ->andWhere(['account_id' => $this->account->id])
            ->andWhere(['id' => $ids])
            ->orderBy('id ASC')
            ->asArray()
            ->all();
    }
}