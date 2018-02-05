<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 13.01.2018
 */

namespace app\commands;


use app\models\Tag;
use yii\console\Controller;
use yii\db\Expression;

class TagController extends Controller
{
    /**
     * Find main tags and update children's.
     */
    public function actionMain()
    {
        $mainTags = Tag::findAll(new Expression('name=slug'));
        foreach ($mainTags as $mainTag) {
            $tags = Tag::find()
                ->andWhere(['slug' => $mainTag->slug])
                ->andWhere(['not', ['id' => $mainTag->id]])
                ->all();
            if ($tags) {
                $this->stdout("Main tag: {$mainTag->name}:\n");
            }
            foreach ($tags as $tag) {
                $this->stdout("\t- {$tag->name}\n");
                $tag->link('mainTag', $mainTag);
            }
        }
    }
}