<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2018-11-08
 */

namespace app\modules\admin\models\account;


use app\components\visualizations\DateHelper;
use app\models\Account;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StatsSearch extends Model
{
    public function search(Account $model)
    {
        $query = $model->getAccountStats();

        $dateRange = DateHelper::getRangeFromUrl();
        /**
         * @var \Carbon\Carbon $start
         * @var \Carbon\Carbon $end
         */
        list($start, $end) = DateHelper::normalizeRange($dateRange);
        $query->andWhere(['>=', 'created_at', $start->toDateTimeString()])
            ->andWhere(['<=', 'created_at', $end->toDateTimeString()]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
    }
}