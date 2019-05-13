<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\components;


use app\components\traits\BatchInsertCommand;
use app\components\traits\FindOrCreate;
use app\components\updaters\TagUpdater;
use app\models\Media;
use app\models\MediaTag;
use app\models\Tag;
use DateTime;
use yii\base\Component;
use yii\helpers\Inflector;

class TagManager extends Component
{
    use FindOrCreate, BatchInsertCommand;

    public function startMonitoring(string $name, $proxyId = null): Tag
    {
        /** @var Tag $tag */
        $tag = $this->findOrCreate(['name' => $name], Tag::class);

        $tagUpdater = \Yii::createObject([
            'class' => TagUpdater::class,
            'tag' => $tag,
        ]);
        $tagUpdater
            ->setMonitoring($proxyId)
            ->save();

        return $tag;
    }

    public function addToMedia(Media $media, array $tags)
    {
        $this->saveTags($tags);

        $rows = array_map(function ($id) use ($media) {
            return [
                $media->id,
                $id,
                $media->taken_at,
            ];
        }, Tag::find()
            ->andWhere(['name' => $tags])
            ->column());

        $this->batchInsertIgnoreCommand(MediaTag::tableName(), ['media_id', 'tag_id', 'created_at'], $rows)
            ->execute();
    }

    /**
     * @param array|string[] $tags Names array
     * @throws \yii\db\Exception
     */
    public function saveTags(array $tags)
    {
        $createdAt = (new DateTime())->format('Y-m-d H:i:s');
        $rows = array_map(function ($tag) use ($createdAt) {
            return [
                $tag,
                Inflector::slug($tag),
                $createdAt,
                $createdAt,
            ];
        }, $tags);

        $this->batchInsertIgnoreCommand(Tag::tableName(), ['name', 'slug', 'updated_at', 'created_at'], $rows)
            ->execute();
    }
}