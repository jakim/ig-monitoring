<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.06.2018
 */

namespace app\components;


use app\components\instagram\models\Post;
use app\models\Media;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class MediaUpdater extends Component
{
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

    public function details(Post $post): Media
    {
        $this->media->instagram_id = $post->id;
        $this->media->shortcode = $post->shortcode;
        $this->media->is_video = $post->isVideo;
        $this->media->caption = $post->caption;
        $this->media->taken_at = (new \DateTime('@' . $post->takenAt))->format('Y-m-d H:i:s');
        $this->media->likes = $post->likes;
        $this->media->comments = $post->comments;

        $this->saveModel($this->media);

        return $this->media;
    }

    private function saveModel(ActiveRecord $model)
    {
        if (!$model->save()) {
            throw new ServerErrorHttpException(sprintf('Validation: %s', json_encode($model->errors)));
        }

        return true;
    }
}