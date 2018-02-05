<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\components;


use yii\base\Component;
use yii\caching\Cache;
use yii\di\Instance;

class UserAgent extends Component
{
    public $cache = 'cache';

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function random()
    {
        /** @var \yii\caching\Cache $cache */
        $cache = Instance::ensure($this->cache);

        $items = $cache->getOrSet(__METHOD__, function (Cache $cache) {
            return (new \jakim\ua\UserAgent())->fetch();
        }, 60 * 60 * 24 * 30);

        shuffle($items);

        return $items['0'];
    }
}