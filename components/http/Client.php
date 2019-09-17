<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 16.01.2018
 */

namespace app\components\http;


use app\components\ArrayHelper;
use app\models\Proxy;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;

class Client
{
    public static function factory(Proxy $proxy, array $config = [], $cacheTtl = 0)
    {
        if ($cacheTtl) {
            $stack = HandlerStack::create();
            $stack->push(new CacheMiddleware(
                new GreedyCacheStrategy(
                    new CacheStorage(), $cacheTtl)
            ), 'cache');
            $config['handler'] = $stack;
        }

        $config = ArrayHelper::merge([
            'proxy' => $proxy->curlString,
            'headers' => [
                'User-Agent' => (new UserAgent())->random(),
            ],
        ], $config);

        return new \GuzzleHttp\Client($config);
    }
}