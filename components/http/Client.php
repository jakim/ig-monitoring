<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 16.01.2018
 */

namespace app\components\http;


use app\components\ArrayHelper;
use app\components\UserAgent;
use app\models\Proxy;

class Client
{
    public static function factory(Proxy $proxy, array $config = [])
    {
        $config = ArrayHelper::merge([
            'proxy' => $proxy->curlString,
            'headers' => [
                'User-Agent' => (new UserAgent())->random(),
            ],
        ], $config);

        return new \GuzzleHttp\Client($config);
    }
}