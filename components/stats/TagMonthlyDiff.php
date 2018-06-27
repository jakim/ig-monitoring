<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.06.2018
 */

namespace app\components\stats;


use app\components\stats\base\TagDiff;
use Carbon\Carbon;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class TagMonthlyDiff extends TagDiff
{
    public function getLastDiff($tagId = null)
    {
        return $this->getFromCache($this->lastDiffCache, $tagId);
    }

    public function initLastDiff()
    {
        $tagIds = $this->getModelIds();

        $max1Ids = $this->getLastStatsIds(null, null, $tagIds);
        $max2Ids = $this->getLastStatsBeforeIds(null, null, $tagIds, 'tag_stats.tag_id', 30, $max1Ids);

        $tagsStats = $this->getTagsStats($tagIds, ArrayHelper::merge($max1Ids, $max2Ids));
        $this->lastDiffCache = $this->prepareCache($tagsStats, 'tag_id');
    }

    public function initDiff($olderDate, $newerDate = null)
    {
        $olderDate = (new Carbon($olderDate))->subDay()->startOfDay()->toDateTimeString();
        $newerDate = (new Carbon($newerDate))->endOfDay()->toDateTimeString();

        if (empty($this->diffCache)) {
            $tagIds = ArrayHelper::getColumn($this->models, 'id');

            //group by DATE Y-m
            $ids = $this->getLastStatsIds($olderDate, $newerDate, $tagIds, [
                'tag_id',
                new Expression('DATE_FORMAT(created_at, \'%Y-%m\')'),
            ]);

            $tagsStats = $this->getTagsStats($tagIds, $ids);

            $this->diffCache = $this->prepareCache($tagsStats, 'tag_id');
        }

        return $this;
    }
}