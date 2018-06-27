<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.06.2018
 */

namespace app\components\stats;


use app\components\stats\base\AccountDiff;
use Carbon\Carbon;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class AccountMonthlyDiff extends AccountDiff
{
    public function initLastDiff()
    {
        $accountIds = $this->getModelIds();

        $max1Ids = $this->getLastStatsIds(null, null, $accountIds);
        $max2Ids = $this->getLastStatsBeforeIds(null, null, $accountIds, 'account_stats.account_id', 30, $max1Ids);

        $accountsStats = $this->getAccountsStats($accountIds, ArrayHelper::merge($max1Ids, $max2Ids));
        $this->lastDiffCache = $this->prepareCache($accountsStats, 'account_id');
    }

    public function initDiff($olderDate, $newerDate = null)
    {
        $olderDate = (new Carbon($olderDate))->subDay()->startOfDay()->toDateTimeString();
        $newerDate = (new Carbon($newerDate))->endOfDay()->toDateTimeString();

        if (empty($this->diffCache)) {
            $accountIds = ArrayHelper::getColumn($this->models, 'id');

            //group by DATE Y-m
            $ids = $this->getLastStatsIds($olderDate, $newerDate, $accountIds, [
                'account_id',
                new Expression('DATE_FORMAT(created_at, \'%Y-%m\')'),
            ]);

            $accountsStats = $this->getAccountsStats($accountIds, $ids);

            $this->diffCache = $this->prepareCache($accountsStats, 'account_id');
        }

        return $this;
    }
}