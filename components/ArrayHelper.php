<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\components;


use yii\helpers\BaseArrayHelper;
use yii\helpers\ReplaceArrayValue;

class ArrayHelper extends BaseArrayHelper
{
    public static function arrayMap($data, array $map): array
    {
        $res = [];
        foreach ($map as $to => $from) {
            $res[$to] = static::getValue($data, $from);
        }

        return $res;
    }
}