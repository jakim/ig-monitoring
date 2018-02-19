<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.02.2018
 */

namespace app\components\jakim\ig;


class Url
{
    public static $baseUrl = 'https://www.instagram.com';

    public static function account($username)
    {
        return static::$baseUrl . str_replace('{username}', $username, '/{username}/');
    }
}