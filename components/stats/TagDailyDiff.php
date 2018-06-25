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

/**
 * @inheritdoc
 *
 * @property array|\app\models\Tag[] $models
 */
class TagDailyDiff extends TagDiff
{
    public function initLastDiff()
    {
        $modelIds = $this->getModelIds();
        $max1Ids = $this->getLastStatsIds(null, null, $modelIds);
        $max2Ids = $this->getLastStatsBeforeIds(null, null, $modelIds, 'tag_stats.tag_id', 1, $max1Ids);

        $tagsStats = $this->getTagsStats($modelIds, ArrayHelper::merge($max1Ids, $max2Ids));
        $this->lastDiffCache = $this->prepareCache($tagsStats, 'tag_id');
    }

    /**
     * The difference between the data from subsequent days from a given date range.
     *
     * @param $olderDate
     * @param null $newerDate
     */
    public function initDiff($olderDate, $newerDate = null)
    {
        $olderDate = (new Carbon($olderDate))->subDay()->startOfDay()->toDateTimeString();
        $newerDate = (new Carbon($newerDate))->endOfDay()->toDateTimeString();

        if (empty($this->diffCache)) {
            $tagIds = $this->getModelIds();

            //group by DATE Y-m-d
            $ids = $this->getLastStatsIds($olderDate, $newerDate, $tagIds, [
                'tag_id',
                new Expression('DATE(created_at)'),
            ]);

            $tagsStats = $this->getTagsStats($tagIds, $ids);

            $this->diffCache = $this->prepareCache($tagsStats, 'tag_id');
        }
    }
}