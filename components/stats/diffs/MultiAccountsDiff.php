<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-24
 */

namespace app\components\stats\diffs;


use app\components\stats\base\BaseDiff;
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
class MultiAccountsDiff extends BaseDiff
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