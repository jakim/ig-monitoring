<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\components\jakim\ig;


class Shortcode
{
    protected static $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

    public static function fromID($id)
    {
        $id = explode('_', $id)[0];
        $code = '';
        while ($id > 0) {
            $remainder = $id % 64;
            $id = ($id - $remainder) / 64;
            $code = static::$chars{$remainder} . $code;
        };

        return $code;
    }

    public static function toID($shortcode)
    {
        $id = 0;
        for ($i = 0; $i < strlen($shortcode); $i++) {
            $c = $shortcode[$i];
            $id = $id * 64 + strpos(static::$chars, $c);
        }

        return $id;
    }
}