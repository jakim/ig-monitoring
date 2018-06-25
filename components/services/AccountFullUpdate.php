<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 18.06.2018
 */

namespace app\components\services;


use app\components\AccountUpdater;
use app\components\http\Client;
use app\components\http\ProxyManager;
use app\components\instagram\AccountScraper;
use app\components\instagram\models\Account;
use app\components\MediaManager;
use app\components\services\contracts\ServiceInterface;
use GuzzleHttp\Exception\ClientException;
use yii\base\BaseObject;
use yii\web\NotFoundHttpException;

class AccountFullUpdate extends BaseObject implements ServiceInterface
{
    /**
     * @var \app\models\Account
     */
    public $account;

    public function run()
    {
        $proxyManager = \Yii::createObject(ProxyManager::class);

        try {
            $proxy = $proxyManager->reserve($this->account);
            $httpClient = Client::factory($proxy, [], 3600);

            $scraper = \Yii::createObject(AccountScraper::class, [
                $httpClient,
            ]);

            $accountData = $this->fetchAccountData($scraper);
            $posts = $scraper->fetchLastPosts($accountData->username);

            $proxyManager->release($proxy);
            unset($proxy);

            $this->updateAccount($accountData, $posts);

        } catch (NotFoundHttpException $exception) {
            $this->account->disabled = 1;
            $this->account->save();
        } finally {
            if (isset($proxy)) {
                $proxyManager->release($proxy);
            }
        }

    }

    private function fetchAccountData(AccountScraper $scraper): Account
    {
        $idents = array_filter([
            $this->account->username,
            $this->account->instagram_id,
        ]);

        foreach ($idents as $ident) {
            try {
                $accountData = $scraper->fetchOne($ident);
            } catch (ClientException $exception) {
                \Yii::error($exception->getMessage(), __METHOD__);
                continue;
            };
            break;
        }

        if (empty($accountData)) {
            throw new NotFoundHttpException();
        }

        return $accountData;
    }

    private function updateAccount($accountData, $posts): void
    {
        $updater = \Yii::createObject([
            'class' => AccountUpdater::class,
            'account' => $this->account,
        ]);

        $updater->details($accountData);

        $stats = $updater->stats($accountData);
        $updater->er($stats, $posts);

        $mediaManager = \Yii::createObject(MediaManager::class);
        $mediaManager->saveForAccount($this->account, $posts);
    }
}