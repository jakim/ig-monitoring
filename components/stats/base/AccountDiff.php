<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 20.06.2018
 */

namespace app\components\stats\base;


use app\models\AccountStats;
use yii\db\Expression;

abstract class AccountDiff extends Diff
{
    protected $statsAttributes = [
        'media',
        'followed_by',
        'follows',
        'er',
    ];

    protected function getLastStatsIds($olderDate, $newerDate, $accountIds, $groupBy = 'account_stats.account_id', $ignoredIds = null)
    {
        $q = AccountStats::find()->cache(5)
            ->select(new Expression('MAX(id) as id'))
            ->andWhere(['account_id' => $accountIds])
            ->andFilterWhere(['>=', 'created_at', $olderDate])
            ->andFilterWhere(['<=', 'created_at', $newerDate])
            ->andFilterWhere(['not', ['id' => $ignoredIds]])
            ->groupBy($groupBy);

        return $q->column();
    }

    protected function getLastStatsBeforeIds($olderDate, $newerDate, $accountIds, $groupBy = 'account_stats.account_id', $beforeInDays = 1, $ignoredIds = null)
    {
        $q = AccountStats::find()
            ->select(new Expression('MAX(id) as id'))
            ->andWhere(['account_stats.account_id' => $accountIds])
            ->andFilterWhere(['>=', 'created_at', $olderDate])
            ->andFilterWhere(['<=', 'created_at', $newerDate])
            ->andFilterWhere(['not', ['account_stats.account_id' => $ignoredIds]])
            ->leftJoin([
                'as_max' => AccountStats::find()
                    ->select([
                        'account_id',
                        new Expression('MAX(created_at) as created_at'),
                    ])
                    ->andWhere(['account_id' => $accountIds])
                    ->groupBy('account_id'),
            ], 'account_stats.account_id=as_max.account_id')
            ->andWhere(['<', 'account_stats.created_at', new Expression(sprintf('DATE_SUB(DATE_SUB(DATE(as_max.created_at), interval %d DAY), interval 1 second)', $beforeInDays - 1))])
            ->groupBy($groupBy);

        return $q->column();
    }

    /**
     * @param $accountIds
     * @param $ids
     * @return array
     */
    protected function getAccountsStats($accountIds, $ids)
    {
        $accountsStats = AccountStats::find()
            ->select([
                'account_stats.*',
                new Expression('DATE(created_at) as created_at'),
            ])
            ->andWhere(['account_id' => $accountIds])
            ->andWhere(['id' => $ids])
            ->orderBy('account_id ASC, id ASC')
            ->asArray()
            ->all();

        return $accountsStats;
    }
}