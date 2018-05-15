<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 14.05.2018
 */

namespace app\modules\api\v1\models;


use app\models\AccountStats;
use app\models\AccountTag;
use yii\base\Model;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

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
            $tags = StringHelper::explode($tags, ',', true, true);
            $tag = array_shift($tags);
            $accountIds = $this->getTaggedAccountIds($tag);

            foreach ($tags as $tag) {
                $accountIds = array_intersect($accountIds, $this->getTaggedAccountIds($tag));
            }

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

        return $dataProvider;
    }

    /**
     * @param $tag
     * @return array
     */
    private function getTaggedAccountIds($tag): array
    {
        $accountIds = AccountTag::find()
            ->select('account_id')
            ->innerJoinWith('tag')
            ->andFilterWhere(['tag.slug' => Inflector::slug($tag)])
            ->column();

        return $accountIds;
    }
}