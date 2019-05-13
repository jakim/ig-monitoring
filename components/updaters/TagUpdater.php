<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 21.06.2018
 */

namespace app\components\updaters;


use app\components\instagram\models\Tag;
use app\components\traits\NextUpdateCalculator;
use app\components\traits\SaveModelTrait;
use app\components\traits\SetTag;
use app\models\TagStats;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Expression;

class TagUpdater extends Component
{
    use SaveModelTrait, SetTag, NextUpdateCalculator;

    public function init()
    {
        parent::init();
        $this->throwExceptionIfTagIsEmpty();
    }

    public function setDetails(Tag $tag)
    {
        $this->tag->name = $tag->name;

        return $this;
    }

    public function setMonitoring($proxyId = null, $proxyTagId = null)
    {
        $this->tag->monitoring = 1;
        $this->tag->proxy_id = $proxyId;
        $this->tag->proxy_tag_id = $proxyTagId;

        return $this;
    }

    public function setIsValid()
    {
        $this->tag->is_valid = 1;
        $this->tag->invalidation_count = 0;
        $this->tag->invalidation_type_id = null;

        return $this;
    }

    public function setIsInvalid(?int $invalidationType = null)
    {
        $this->tag->is_valid = 0;
        $this->tag->invalidation_count = (int)$this->tag->invalidation_count + 1;
        $this->tag->invalidation_type_id = $invalidationType;

        return $this;
    }

    /**
     * If true, then will be automatically calculate from invalidation_count
     *
     * @param true|int|null $interval
     * @return $this
     */
    public function setNextStatsUpdate($interval = 24)
    {
        $this->tag->update_stats_after = $this->getNextUpdateDate($this->tag, $interval);

        return $this;
    }

    public function setStats(Tag $tag, bool $createHistory = true)
    {
        $this->tag->touch('stats_updated_at');
        if ($this->tag->media === null || $this->statsNeedUpdate($tag)) {
            $this->tag->media = $tag->media;
            $this->tag->likes = $tag->likes;
            $this->tag->min_likes = $tag->minLikes;
            $this->tag->max_likes = $tag->maxLikes;
            $this->tag->comments = $tag->comments;
            $this->tag->min_comments = $tag->minComments;
            $this->tag->max_comments = $tag->maxComments;

            if ($createHistory) {
                $this->createHistory();
            }
        }

        return $this;
    }

    public function save()
    {
        $this->saveModel($this->tag);
    }

    protected function createHistory()
    {
        $tagStats = new TagStats([
            'tag_id' => $this->tag->id,
            'media' => $this->tag->media,
            'likes' => $this->tag->likes,
            'min_likes' => $this->tag->min_likes,
            'max_likes' => $this->tag->max_likes,
            'comments' => $this->tag->comments,
            'min_comments' => $this->tag->min_comments,
            'max_comments' => $this->tag->max_comments,
        ]);
        $this->saveModel($tagStats);

        return $tagStats;
    }

    private function statsNeedUpdate(Tag $tag)
    {
        return $this->tag->media != $tag->media ||
            $this->tag->likes != $tag->likes ||
            $this->tag->comments != $tag->comments ||
            $this->tag->min_likes != $tag->minLikes ||
            $this->tag->max_likes != $tag->maxLikes ||
            $this->tag->min_comments != $tag->minComments ||
            $this->tag->max_comments != $tag->maxComments;
    }
}