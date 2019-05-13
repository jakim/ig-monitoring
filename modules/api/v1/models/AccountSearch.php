<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 14.05.2018
 */

namespace app\modules\api\v1\models;


use app\components\AccountManager;
use app\models\AccountStats;
use yii\base\Model;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class AccountSearch extends Model
{
    public function search(array $params)
    {
        $query = Account::find()
            ->leftJoin(
                AccountStats::tableName(),
                'account.id=account_stats.account_id and account_stats.id = (SELECT MAX(id) FROM account_stats WHERE account_stats.account_id=account.id)'
            );

        $tags = ArrayHelper::getValue($params, 'filter.tags');
        unset($params['filter']['tags']);

        if ($tags) {
            $manager = \Yii::createObject(AccountManager::class);
            $accountIds = $manager->findByCategories($tags, \Yii::$app->user->id);

            $query->andWhere(['account.id' => $accountIds]);
        }

        $dataFilter = \Yii::createObject([
            'class' => ActiveDataFilter::class,
            'searchModel' => DataFilterForm::class,
        ]);
        if ($dataFilter->load($params)) {
            $filter = $dataFilter->build();
            if ($filter === false) {
                return $dataFilter;
            }
            $query->andWhere($filter);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['followed_by'] = [
            'asc' => ['account_stats.followed_by' => SORT_ASC],
            'desc' => ['account_stats.followed_by' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['follows'] = [
            'asc' => ['account_stats.follows' => SORT_ASC],
            'desc' => ['account_stats.follows' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['media'] = [
            'asc' => ['account_stats.media' => SORT_ASC],
            'desc' => ['account_stats.media' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['er'] = [
            'asc' => ['account_stats.er' => SORT_ASC],
            'desc' => ['account_stats.er' => SORT_DESC],
        ];

        return $dataProvider;
    }
}