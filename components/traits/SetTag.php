<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-02-13
 */

namespace app\components\traits;


use app\models\Tag;
use yii\base\InvalidConfigException;

trait SetTag
{
    /**
     * @var \app\models\Tag
     */
    public $tag;

    public function throwExceptionIfTagIsEmpty()
    {
        if (!$this->tag instanceof Tag) {
            throw new InvalidConfigException('Property \'tag\' must be set and be type of \'\app\models\Tag\'.');
        }
    }
}