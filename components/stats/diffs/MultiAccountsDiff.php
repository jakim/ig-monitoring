<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-24
 */

namespace app\components\stats\diffs;


use app\components\stats\base\BaseMultiDiff;
use app\models\AccountStats;
use yii\base\InvalidConfigException;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class MultiAccountsDiffDataProvider
 *
 * @package app\components\stats\providers
 *
 * @property \Carbon\Carbon $from
 * @property \Carbon\Carbon $to
 */
class MultiAccountsDiff extends BaseMultiDiff
{
    /**
     * @var \app\models\Account[]|array
     */
    public $accounts;

    public $accountIds = [];

    public function setAccounts(array $accounts)
    {
        $this->accounts = $accounts;

        return $this;
    }

    public function init()
    {
        parent::init();
        if ($this->accounts === null) {
            throw new InvalidConfigException('Property \'accounts\' can not be empty.');
        }
        $this->accountIds = $this->accountIds ?: ArrayHelper::getColumn($this->accounts, 'id');
        $this->throwExceptionIfFromToAreNotSet();
        $this->throwExceptionIfStatsAttributesIsNotSet();
    }

    protected function findStatsIds(?string $date, array $ignoredStatsIds = [])
    {
        return AccountStats::find()
            ->cache()
            ->select(new Expression('MAX(id) as id'))
            ->indexBy('account_id')
            ->andWhere(['account_id' => $this->accountIds])
            ->andWhere(['<=', 'created_at', $date])
            ->andFilterWhere(['not', ['id' => $ignoredStatsIds]])
            ->groupBy('account_id')
            ->column();
    }

    protected function findDataModels(array $statsIds)
    {
        $columns = array_map(function ($attr) {
            return "account_stats.{$attr}";
        }, $this->statsAttributes);
        $columns[] = 'id';
        $columns[] = 'account_id';
        $columns[] = new Expression('DATE(created_at) as created_at');

        return AccountStats::find()
            ->cache()
            ->select($columns)
            ->indexBy('account_id')
            ->andWhere(['id' => $statsIds])
            ->asArray()
            ->all();
    }
}