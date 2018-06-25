<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 21.06.2018
 */

namespace app\components;


use app\components\instagram\models\Tag;
use app\models\TagStats;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class TagUpdater extends Component
{
    /**
     * @var \app\models\Tag
     */
    public $tag;

    public function init()
    {
        parent::init();
        if (!$this->tag instanceof \app\models\Tag) {
            throw new InvalidConfigException('Property \'tag\' must be set and be type od \'\app\models\Tag\'.');
        }
    }

    public function stats(Tag $tag): TagStats
    {
        $tagStats = $this->tag->lastTagStats;
        if ($tagStats === null || $this->statsNeedUpdate($tagStats, $tag)) {
            $tagStats = new TagStats([
                'tag_id' => $this->tag->id,
                'media' => $tag->media,
                'likes' => $tag->likes,
                'min_likes' => $tag->minLikes,
                'max_likes' => $tag->maxLikes,
                'comments' => $tag->comments,
                'min_comments' => $tag->minComments,
                'max_comments' => $tag->maxComments,
            ]);
        }
        $this->saveModel($tagStats);

        return $tagStats;
    }

    private function statsNeedUpdate(TagStats $tagStats, Tag $tag)
    {
        return $tagStats->media != $tag->media ||
            $tagStats->likes != $tag->likes ||
            $tagStats->comments != $tag->comments ||
            $tagStats->min_likes != $tag->minLikes ||
            $tagStats->max_likes != $tag->maxLikes ||
            $tagStats->min_comments != $tag->minComments ||
            $tagStats->max_comments != $tag->maxComments;
    }

    private function saveModel(ActiveRecord $model)
    {
        if (!$model->save()) {
            throw new ServerErrorHttpException(sprintf('Validation: %s', json_encode($model->errors)));
        }

        return true;
    }
}