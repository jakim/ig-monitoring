<?php

namespace app\modules\admin\models;

use app\models\TagStats;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TagSearch represents the model behind the search form of `app\models\Tag`.
 */
class TagSearch extends Tag
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'main_tag_id', 'monitoring', 'proxy_id'], 'integer'],
            [['name', 'slug'], 'safe'],
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
        $query = Tag::find()
            ->select([
                'tag.*',
                'tag_stats.media as ts_media',
                'tag_stats.likes as ts_likes',
                'tag_stats.comments as ts_comments',
                'tag_stats.min_likes as ts_min_likes',
                'tag_stats.max_likes as ts_max_likes',
                'tag_stats.min_comments as ts_min_comments',
                'tag_stats.max_comments as ts_max_comments',
            ])
            ->leftJoin(
                TagStats::tableName(),
                'tag.id=tag_stats.tag_id and tag_stats.id = (SELECT MAX(id) FROM tag_stats WHERE tag_stats.tag_id=tag.id)'
            );

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['ts_media'] = [
            'asc' => ['ts_media' => SORT_ASC],
            'desc' => ['ts_media' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['ts_likes'] = [
            'asc' => ['ts_likes' => SORT_ASC],
            'desc' => ['ts_likes' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['ts_comments'] = [
            'asc' => ['ts_comments' => SORT_ASC],
            'desc' => ['ts_comments' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['ts_min_likes'] = [
            'asc' => ['ts_min_likes' => SORT_ASC],
            'desc' => ['ts_min_likes' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['ts_max_likes'] = [
            'asc' => ['ts_max_likes' => SORT_ASC],
            'desc' => ['ts_max_likes' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['ts_min_comments'] = [
            'asc' => ['ts_min_comments' => SORT_ASC],
            'desc' => ['ts_min_comments' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['ts_max_comments'] = [
            'asc' => ['ts_max_comments' => SORT_ASC],
            'desc' => ['ts_max_comments' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'main_tag_id' => $this->main_tag_id,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'monitoring' => $this->monitoring,
            'proxy_id' => $this->proxy_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }
}
