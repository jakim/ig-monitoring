<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.05.2018
 */

namespace app\tests\fixtures;


use app\models\Tag;
use yii\test\ActiveFixture;

class TagFixture extends ActiveFixture
{
    public $modelClass = Tag::class;
}