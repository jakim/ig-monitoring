<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2018-11-11
 */

namespace app\modules\admin\models\tag;


use app\components\visualizations\DateHelper;
use app\models\Tag;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StatsSearch extends Model
{
    public function search(Tag $model)
    {
        $query = $model->getTagStats();

        $dateRange = DateHelper::getRangeFromUrl();
        list($start, $end) = DateHelper::normalizeRange($dateRange);
        /**
         * @var \Carbon\Carbon $start
         * @var \Carbon\Carbon $end
         */
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