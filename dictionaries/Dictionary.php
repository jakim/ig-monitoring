<?php

namespace app\dictionaries;


use yii\helpers\ArrayHelper;

abstract class Dictionary
{
    abstract public static function labels(): array;

    public static function getLabel($key, $defaultValue = '')
    {
        return ArrayHelper::getValue(static::labels(), $key, $defaultValue);
    }
}