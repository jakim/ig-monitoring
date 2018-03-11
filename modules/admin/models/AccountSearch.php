<?php

namespace app\modules\admin\models;

use app\models\AccountStats;
use app\models\AccountTag;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AccountSearch represents the model behind the search form of `app\models\Account`.
 */
class AccountSearch extends Account
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'monitoring', 'proxy_id'], 'integer'],
            [['username', 'profile_pic_url', 'full_name', 'biography', 'external_url', 'instagram_id', 's_tags'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Account::find()
            ->select([
                'account.*',
                'account_stats.followed_by as as_followed_by',
                'account_stats.follows as as_follows',
                'account_stats.media as as_media',
                'account_stats.er as as_er',
            ])
            ->leftJoin(
                AccountStats::tableName(),
                'account.id=account_stats.account_id and account_stats.id = (SELECT MAX(id) FROM account_stats WHERE account_stats.account_id=account.id)'
            );

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['as_followed_by'] = [
            'asc' => ['as_followed_by' => SORT_ASC],
            'desc' => ['as_followed_by' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['as_follows'] = [
            'asc' => ['as_follows' => SORT_ASC],
            'desc' => ['as_follows' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['as_media'] = [
            'asc' => ['as_media' => SORT_ASC],
            'desc' => ['as_media' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['as_er'] = [
            'asc' => ['as_er' => SORT_ASC],
            'desc' => ['as_er' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'monitoring' => $this->monitoring,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username]);

        if ($this->s_tags) {
            $accountIds = AccountTag::find()
                ->select('account_id')
                ->innerJoinWith('tag')
                ->andFilterWhere(['like', 'tag.name', $this->s_tags])
                ->column();
            $query->andWhere(['account.id' => $accountIds]);
        }

        return $dataProvider;
    }
}
