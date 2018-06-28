<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.06.2018
 */

namespace app\components\stats;


use app\models\Account;
use app\models\AccountStats;
use Carbon\Carbon;
use yii\base\Component;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class AccountDaily extends Component
{
    /**
     * @var \app\models\Account
     */
    public $model;

    private $statsAttributes = [
        'media',
        'followed_by',
        'follows',
        'er',
    ];

    protected $cache = [];

    public function __construct(Account $model, array $config = [])
    {
        $this->model = $model;
        $this->cache = [];
        parent::__construct($config);
    }

    public function get()
    {
        return $this->cache;
    }

    /**
     * The difference between the data from subsequent days from a given date range.
     *
     * @param $olderDate
     * @param null $newerDate
     * @return \app\components\stats\AccountDaily
     */
    public function initData($olderDate, $newerDate = null)
    {
        $olderDate = (new Carbon($olderDate))->startOfDay()->toDateTimeString();
        $newerDate = (new Carbon($newerDate))->endOfDay()->toDateTimeString();

        if (empty($this->cache)) {
            //group by DATE Y-m-d
            $ids = AccountStats::find()
                ->select(new Expression('MAX(id) as id'))
                ->andWhere(['account_id' => $this->model->id])
                ->andWhere(['>=', 'created_at', $olderDate])
                ->andWhere(['<=', 'created_at', $newerDate])
                ->groupBy(new Expression('DATE(created_at)'))
                ->column();

            $stats = AccountStats::find()
                ->select([
                    'account_stats.*',
                    new Expression('DATE(created_at) as created_at'),
                ])
                ->andWhere(['account_id' => $this->model->id])
                ->andWhere(['id' => $ids])
                ->orderBy('id ASC')
                ->asArray()
                ->all();

            foreach ($stats as $stat) {
                foreach ($this->statsAttributes as $statsAttribute) {
                    $value = ArrayHelper::getValue($stat, $statsAttribute, 0);
                    $this->cache[$stat['created_at']][$statsAttribute] = $value;
                }
            }
        }

        return $this;
    }
}