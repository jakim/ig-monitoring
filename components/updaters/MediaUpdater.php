<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.06.2018
 */

namespace app\components\updaters;


use app\components\instagram\models\Post;
use app\components\traits\SaveModelTrait;
use app\models\Media;
use yii\base\Component;
use yii\base\InvalidConfigException;

class MediaUpdater extends Component
{
    use SaveModelTrait;

    /**
     * @var \app\models\Media
     */
    public $media;

    public function init()
    {
        parent::init();
        if (!$this->media instanceof Media) {
            throw new InvalidConfigException('Property \'media\' must be set and by type od \'\app\models\Media\'');
        }
    }

    public function setDetails(Post $post)
    {
        $this->media->instagram_id = $post->id;
        $this->media->shortcode = $post->shortcode;
        $this->media->is_video = $post->isVideo;
        $this->media->caption = $post->caption;
        $this->media->taken_at = $this->getNormalizedTakenAt($post);
        $this->media->likes = $post->likes;
        $this->media->comments = $post->comments;

        return $this;
    }

    public function save()
    {
        $this->saveModel($this->media);
    }

    protected function getNormalizedTakenAt(Post $post): string
    {
        return (new \DateTime('@' . $post->takenAt))->format('Y-m-d H:i:s');
    }
}