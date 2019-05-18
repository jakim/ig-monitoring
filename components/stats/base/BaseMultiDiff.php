<?php
/**
 * Created for monitoring-free.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-05-18
 */

namespace app\components\stats\base;


use yii\helpers\ArrayHelper;

abstract class BaseMultiDiff extends BaseDiff
{
    protected function prepareData(): array
    {
        $models = [];

        $dbTo = $this->to->copy()->endOfDay()->toDateTimeString();
        $dbFrom = $this->from->copy()->endOfDay()->toDateTimeString();

        $toStatsIds = $this->findStatsIds($dbTo);
        $toModels = $this->findDataModels($toStatsIds);

        // jesli znaleziona ostatnia data 'do' jest wczesniejsza niz od, to nie pomijaj modelu
        $ignoredModels = array_filter($toModels, function ($toModel) use ($dbFrom) {
            if (strtotime($toModel['created_at']) > strtotime($dbFrom)) {
                return true;
            }
        });

        $fromStatsIds = $this->findStatsIds($dbFrom, ArrayHelper::getColumn($ignoredModels, 'id'));
        $fromModels = $this->findDataModels($fromStatsIds);

        foreach ($toModels as $accountId => $toModel) {
            $fromModel = ArrayHelper::getValue($fromModels, $accountId);
            foreach ($this->statsAttributes as $statsAttribute) {
                $value = ArrayHelper::getValue($toModel, $statsAttribute, 0) - ArrayHelper::getValue($fromModel, $statsAttribute, 0);
                $models[$accountId][$statsAttribute] = $value;
            }
        }

        return $models;
    }

    abstract protected function findStatsIds(?string $date, array $ignoredStatsIds = []);

    abstract protected function findDataModels(array $statsIds);
}