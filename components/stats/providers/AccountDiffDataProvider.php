<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-24
 */

namespace app\components\stats\providers;


use app\components\stats\contracts\DataProviderInterface;
use app\dictionaries\Grouping;
use yii\helpers\ArrayHelper;

class AccountDiffDataProvider extends AccountDataProvider implements DataProviderInterface
{
    protected function prepareModels()
    {
        $models = [];

        $dbFrom = $this->from->copy();
        switch ($this->grouping) {
            case Grouping::MONTHLY:
                $dbFrom = $dbFrom->subMonth()->endOfMonth();
                break;
            case Grouping::WEEKLY:
                $dbFrom = $dbFrom->subWeek()->endOfWeek();
                break;
            case Grouping::DAILY:
            default:
                $dbFrom = $dbFrom->subDay()->endOfDay();
        }
        $dbFrom = $this->findDbDate($dbFrom);

        $dbTo = $this->to->copy()->endOfDay()->toDateTimeString();

        $idsQuery = $this->findStatsIds($dbFrom, $dbTo, false);
        $data = $this->findDataModels($idsQuery);

        $older = array_shift($data);
        foreach ($data as $stats) {
            foreach ($this->statsAttributes as $statsAttribute) {
                $value = ArrayHelper::getValue($stats, $statsAttribute, 0) - ArrayHelper::getValue($older, $statsAttribute, 0);
                $models[$stats['created_at']][$statsAttribute] = $value;
            }
            $older = $stats;
        }

        return $models;
    }
}