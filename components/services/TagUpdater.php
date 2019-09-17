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
use app\components\builders\TagBuilder;
use app\dictionaries\TagInvalidationType;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Yii;

class TagUpdater implements ServiceInterface
{
    /**
     * @var \app\models\Tag
     */
    public $tag;

    public function run()
    {
        $proxyManager = Yii::createObject(ProxyManager::class);
        $tagUpdater = Yii::createObject([
            'class' => TagBuilder::class,
            'tag' => $this->tag,
        ]);

        try {
            $proxy = $proxyManager->reserve(true);
            $httpClient = Client::factory($proxy, [], 3600);

            $scraper = Yii::createObject(TagScraper::class, [
                $httpClient,
            ]);

            $tagData = $scraper->fetchOne($this->tag->name);

            $proxyManager->release($proxy);
            unset($proxy);

            $tagUpdater
                ->setIsValid()
                ->setStats($tagData)
                ->setNextStatsUpdate()
                ->save();

        } catch (ClientException $exception) {
            $tagUpdater
                ->setIsInvalid(TagInvalidationType::NOT_FOUND)
                ->setNextStatsUpdate(true)
                ->save();
        } catch (RequestException $exception) {
            $tagUpdater
                ->setIsInvalid()
                ->setNextStatsUpdate(true)
                ->save();
        } finally {
            if (isset($proxy)) {
                $proxyManager->release($proxy);
            }
        }
    }
}