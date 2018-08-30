<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 30.08.2018
 */

namespace app\dictionaries;


class TagInvalidationType extends Dictionary
{
    const NOT_FOUND = 1;

    public static function labels(): array
    {
        return [
            self::NOT_FOUND => 'Not found',
        ];
    }
}