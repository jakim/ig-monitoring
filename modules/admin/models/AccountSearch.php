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
                new Expression('GROUP_CONCAT(tag.name SEPARATOR \', \') as s_tags'),
            ])
            ->leftJoin(AccountTag::tableName(), 'account.id=account_tag.account_id AND account_tag.user_id=' . $userId)
            ->leftJoin(Tag::tableName(), 'account_tag.tag_id=tag.id')
            ->groupBy('account.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->defaultOrder = [
            'is_valid' => SORT_ASC,
            'invalidation_type_id' => SORT_DESC,
            'invalidation_count' => SORT_DESC,
            'id' => SORT_DESC,
        ];

        $dataProvider->sort->attributes['username'] = [
            'asc' => ['name' => SORT_ASC, 'username' => SORT_ASC],
            'desc' => ['name' => SORT_DESC, 'username' => SORT_DESC],
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
