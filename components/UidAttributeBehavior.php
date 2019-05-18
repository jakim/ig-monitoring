<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 24.07.2018
 */

namespace app\components;


use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class UidAttributeBehavior extends AttributeBehavior
{
    /**
     * @var string
     */
    public $attribute = 'uid';

    public $preserveNonEmptyValues = true;

    public function init()
    {
        parent::init();
        $this->attributes = [
            BaseActiveRecord::EVENT_BEFORE_INSERT => $this->attribute,
            BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->attribute,
        ];
    }

    protected function getValue($event)
    {
        do {
            $uid = Yii::$app->security->generateRandomString(64);
            /** @var \yii\db\ActiveRecord $class */
            $class = get_class($this->owner);
            $uidExist = $class::find()
                ->andWhere([$class::tableName() . '.' . $this->attribute => $uid])
                ->exists();
        } while ($uidExist);

        return $uid;
    }
}