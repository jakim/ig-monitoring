<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\jobs;


use app\components\TagManager;
use app\models\Tag;
use yii\queue\JobInterface;
use yii\queue\Queue;

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
            $manager = \Yii::createObject(TagManager::class);
            $manager->update($tag);
        }
    }
}