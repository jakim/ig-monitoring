<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 03.04.2018
 */

namespace app\modules\admin\components;


use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Expression;

class AccountStatsManager extends Component
{
    /**
     * @var \app\modules\admin\models\Account
     */
    public $account;

    /**
     * Daily stats from last month.
     *
     * @var \app\models\AccountStats[]
     */
    protected $dailyStats;

    /**
     * Monthly stats from last year.
     *
     * @var \app\models\AccountStats[]
     */
    protected $monthlyStats;

    public function init()
    {
        parent::init();
        if (!$this->account) {
            throw new InvalidConfigException('Property \'account\' can not be empty.');
        }
    }

    public function lastChange($attribute)
    {
        $statsData = $this->getDailyStatsData();
        if (!$statsData) {
            return null;
        }
        $latest = array_shift($statsData);
        $oldest = array_shift($statsData);

        return $latest->$attribute - $oldest->$attribute;
    }

    public function lastStatsFrom()
    {
        $statsData = $this->getDailyStatsData();
        if (!$statsData) {
            return null;
        }
        array_shift($statsData);
        $oldest = array_shift($statsData);

        return $oldest->created_at;
    }

    public function lastMonthChange($attribute)
    {
        $statsData = $this->getDailyStatsData();
        if (!$statsData) {
            return null;
        }

        $latest = array_shift($statsData);
        $oldest = end($statsData);

        return $latest->$attribute - $oldest->$attribute;
    }

    public function dailyStatsFrom()
    {
        $statsData = $this->getDailyStatsData();
        if (!$statsData) {
            return null;
        }
        array_shift($statsData);
        $oldest = end($statsData);

        return $oldest->created_at;
    }

    public function monthlyStatsFrom()
    {
        $statsData = $this->getMonthlyStatsData();
        if (!$statsData) {
            return null;
        }
        array_shift($statsData);
        $oldest = end($statsData);

        return $oldest->created_at;
    }

    public function getDailyStatsData($reverse = true)
    {
        $this->initDailyStatsData();
        if (count($this->dailyStats) < 2) {
            return null;
        }
        if ($reverse) {
            return array_reverse($this->dailyStats);
        }

        return $this->dailyStats;
    }

    public function getMonthlyStatsData($reverse = true)
    {
        $this->initMonthlyStatsData();
        if (count($this->monthlyStats) < 2) {
            return null;
        }
        if ($reverse) {
            return array_reverse($this->monthlyStats);
        }

        return $this->monthlyStats;
    }

    private function initDailyStatsData()
    {
        if (!$this->dailyStats) {
            $rows = $this->account->getAccountStats()
                ->select([
                    new Expression('DATE_FORMAT(created_at, "%Y-%m-%d") as day'),
                    'account_stats.*',
                ])
                ->andWhere(new Expression('account_stats.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)'))
                ->orderBy('account_stats.id ASC')
                ->all();

            foreach ($rows as $row) {
                $this->dailyStats[$row->day] = $row;
            }
            $this->dailyStats = array_values($this->dailyStats);
        }
    }

    private function initMonthlyStatsData()
    {
        if (!$this->monthlyStats) {
            $rows = $this->account->getAccountStats()
                ->select([
                    new Expression('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    'account_stats.*',
                ])
                ->andWhere(new Expression('account_stats.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)'))
                ->orderBy('account_stats.id ASC')
                ->all();

            foreach ($rows as $row) {
                $this->monthlyStats[$row->month] = $row;
            }
            $this->monthlyStats = array_values($this->monthlyStats);
        }
    }
}