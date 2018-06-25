<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 21.06.2018
 */

namespace app\components\stats\base;


use app\models\TagStats;
use yii\db\Expression;

abstract class TagDiff extends Diff
{
    protected $statsAttributes = [
        'media',
        'likes',
        'min_likes',
        'max_likes',
        'comments',
        'min_comments',
        'max_comments',
    ];

    protected function getLastStatsIds($olderDate, $newerDate, $tagIds, $groupBy = 'tag_stats.tag_id', $ignoredIds = null)
    {
        $q = TagStats::find()->cache(5)
            ->select(new Expression('MAX(id) as id'))
            ->andWhere(['tag_id' => $tagIds])
            ->andFilterWhere(['>=', 'created_at', $olderDate])
            ->andFilterWhere(['<=', 'created_at', $newerDate])
            ->andFilterWhere(['not', ['id' => $ignoredIds]])
            ->groupBy($groupBy);

        return $q->column();
    }

    protected function getLastStatsBeforeIds($olderDate, $newerDate, $tagIds, $groupBy = 'tag_stats.tag_id', $beforeInDays = 1, $ignoredIds = null)
    {
        $q = TagStats::find()
            ->select(new Expression('MAX(id) as id'))
            ->andWhere(['tag_stats.tag_id' => $tagIds])
            ->andFilterWhere(['>=', 'created_at', $olderDate])
            ->andFilterWhere(['<=', 'created_at', $newerDate])
            ->andFilterWhere(['not', ['tag_stats.tag_id' => $ignoredIds]])
            ->leftJoin([
                'as_max' => TagStats::find()
                    ->select([
                        'tag_id',
                        new Expression('MAX(created_at) as created_at'),
                    ])
                    ->andWhere(['tag_id' => $tagIds])
                    ->groupBy('tag_id'),
            ], 'tag_stats.tag_id=as_max.tag_id')
            ->andWhere(['<', 'tag_stats.created_at', new Expression(sprintf('DATE_SUB(DATE_SUB(DATE(as_max.created_at), interval %d DAY), interval 1 second)', $beforeInDays - 1))])
            ->groupBy($groupBy);

        return $q->column();
    }

    /**
     * @param $tagIds
     * @param $ids
     * @return array
     */
    protected function getTagsStats($tagIds, $ids)
    {
        $tagsStats = TagStats::find()
            ->select([
                'tag_stats.*',
                new Expression('DATE(created_at) as created_at'),
            ])
            ->andWhere(['tag_id' => $tagIds])
            ->andWhere(['id' => $ids])
            ->orderBy('tag_id ASC, id ASC')
            ->asArray()
            ->all();

        return $tagsStats;
    }
}