<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 20.08.2018
 */

namespace app\dictionaries;


class AccountInvalidationType extends Dictionary
{
    const IS_PRIVATE = 1;
    const NOT_FOUND = 2;
    const RESTRICTED_PROFILE = 3;
    const PROXY_TIMEOUT = 4;
    const PROXY_ERROR = 5;

    public static function labels(): array
    {
        return [
            self::IS_PRIVATE => 'Is private',
            self::NOT_FOUND => 'Not found',
            self::RESTRICTED_PROFILE => 'Restricted profile',
            self::PROXY_TIMEOUT => 'Proxy Timeout',
            self::PROXY_ERROR => 'Proxy Error',
        ];
    }
}