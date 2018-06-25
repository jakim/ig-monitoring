<?php

namespace app\modules\admin\models;

use app\models\ProxyTag;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\Inflector;

/**
 * ProxySearch represents the model behind the search form of `app\models\Proxy`.
 */
class ProxySearch extends Proxy
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'port'], 'integer'],
            [['ip', 'username', 'password', 'active', 'tagString'], 'safe'],
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
        $query = Proxy::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'port' => $this->port,
        ]);

        $query->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['active' => $this->active]);

        if ($this->tagString) {
            $proxyIds = ProxyTag::find()
                ->select('proxy_tag.proxy_id')
                ->innerJoinWith('tag')
                ->andFilterWhere(['like', 'tag.slug', Inflector::slug($this->tagString)])
                ->column();
            $query->andWhere(['id' => $proxyIds]);
        }

        return $dataProvider;
    }
}
