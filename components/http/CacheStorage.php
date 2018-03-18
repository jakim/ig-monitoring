<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 17.03.2018
 */

namespace app\components\http;


use Kevinrob\GuzzleCache\CacheEntry;
use Kevinrob\GuzzleCache\Storage\CacheStorageInterface;
use yii\caching\Cache;
use yii\di\Instance;

class CacheStorage implements CacheStorageInterface
{

    public $cache;

    /**
     * YiiCacheStorage constructor.
     *
     * @param mixed $cache Cache component.
     */
    public function __construct($cache = 'cache')
    {
        $this->cache = $cache;
    }

    public function fetch($key)
    {
        /** @var \yii\caching\Cache $cache */
        $cache = Instance::ensure($this->cache, Cache::class);

        return $cache->get($key);
    }

    public function save($key, CacheEntry $data)
    {
        /** @var \yii\caching\Cache $cache */
        $cache = Instance::ensure($this->cache, Cache::class);

        return $cache->set($key, $data);
    }

    public function delete($key)
    {
        /** @var \yii\caching\Cache $cache */
        $cache = Instance::ensure($this->cache, Cache::class);

        return $cache->delete($key);
    }
}