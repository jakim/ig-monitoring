<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 21.06.2018
 */

namespace app\components\services;


use app\components\http\Client;
use app\components\http\ProxyManager;
use app\components\instagram\TagScraper;
use app\components\services\contracts\ServiceInterface;
use app\components\TagUpdater;

class TagFullUpdate implements ServiceInterface
{
    /**
     * @var \app\models\Tag
     */
    public $tag;

    public function run()
    {
        $proxyManager = \Yii::createObject(ProxyManager::class);

        try {
            $proxy = $proxyManager->reserve($this->tag);
            $httpClient = Client::factory($proxy, [], 3600);

            $scraper = \Yii::createObject(TagScraper::class, [
                $httpClient,
            ]);

            $tagData = $scraper->fetchOne($this->tag->name);

            $proxyManager->release($proxy);
            unset($proxy);

            $updater = \Yii::createObject([
                'class' =>TagUpdater::class,
                'tag' => $this->tag,
            ]);
            $updater->stats($tagData);

        } finally {
            if (isset($proxy)) {
                $proxyManager->release($proxy);
            }
        }
    }
}