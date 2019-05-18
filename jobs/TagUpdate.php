<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\jobs;


use app\components\services\TagFullUpdate;
use app\models\Tag;
use Yii;
use yii\queue\JobInterface;

class TagUpdate implements JobInterface
{
    public $id;

    /**
     * @param \yii\queue\Queue $queue
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        $tag = Tag::findOne($this->id);
        if ($tag) {
            $service = Yii::createObject([
                'class' => TagFullUpdate::class,
                'tag' => $tag,
            ]);
            $service->run();
        }
    }
}