<?php

namespace app\modules\admin\models;

use app\components\AccountManager;
use app\models\AccountStats;
use app\models\AccountTag;
use app\models\Tag;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

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
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params)
    {
        $userId = (int)\Yii::$app->user->id;

        $query = Account::find()
            ->select([
                'account.*',
                'account_stats.followed_by as as_followed_by',
                'account_stats.follows as as_follows',
                'account_stats.media as as_media',
                'account_stats.er as as_er',
                'account_stats.created_at as as_created_at',
                new Expression('GROUP_CONCAT(tag.name SEPARATOR \', \') as s_tags'),
            ])
            ->leftJoin([
                'as_max' => AccountStats::find()
                    ->select([
                        new Expression('MAX(id) as id'),
                        'account_id',
                    ])
                    ->groupBy('account_id'),
            ], 'account.id=as_max.account_id')
            ->leftJoin(AccountStats::tableName(), 'account_stats.id=as_max.id')
            ->leftJoin(AccountTag::tableName(), 'account.id=account_tag.account_id AND account_tag.user_id=' . $userId)
            ->leftJoin(Tag::tableName(), 'account_tag.tag_id=tag.id')
            ->groupBy('account.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->defaultOrder = [
            'disabled' => SORT_DESC,
            'id' => SORT_DESC,
        ];

        $dataProvider->sort->attributes['username'] = [
            'asc' => ['name' => SORT_ASC, 'username' => SORT_ASC],
            'desc' => ['name' => SORT_DESC, 'username' => SORT_DESC],
        ];

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
        $dataProvider->sort->attributes['as_created_at'] = [
            'asc' => ['as_created_at' => SORT_ASC],
            'desc' => ['as_created_at' => SORT_DESC],
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

        $query->andFilterWhere(['or',
            ['like', 'username', $this->username],
            ['like', 'account.name', $this->username],
        ]);

        if ($this->s_tags) {
            $manager = \Yii::createObject(AccountManager::class);
            $accountIds = $manager->findByTags($this->s_tags, $userId);

            $query->andWhere(['account.id' => $accountIds]);
        }

        return $dataProvider;
    }
}
