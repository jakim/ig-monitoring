<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 18.06.2018
 */

namespace app\components\services;


use app\components\AccountManager;
use app\components\AccountUpdater;
use app\components\http\Client;
use app\components\http\ProxyManager;
use app\components\instagram\AccountScraper;
use app\components\instagram\models\Account;
use app\components\MediaManager;
use app\components\services\contracts\ServiceInterface;
use app\dictionaries\AccountInvalidationType;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
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
        $accountManager = \Yii::createObject(AccountManager::class);

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


            if ($accountData->isPrivate) {
                $accountManager->updateInvalidation($this->account, AccountInvalidationType::IS_PRIVATE);
            } else {
                $this->updateAccount($accountData, $posts);
                $accountManager->markAsValid($this->account);
            }

        } catch (NotFoundHttpException $exception) {
            $accountManager->updateInvalidation($this->account, AccountInvalidationType::NOT_FOUND);
        } catch (RequestException $exception) {
            $accountManager->updateInvalidation($this->account, null);
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
                if ($this->account->instagram_id && $accountData->id != $this->account->instagram_id) {
                    continue;
                }
            } catch (ClientException $exception) {
                \Yii::error($exception->getMessage(), __METHOD__);
                continue;
            }
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

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $updater->details($accountData);
            $updater->stats($accountData, $posts);
            $transaction->commit();
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            throw  $exception;
        }

        $mediaManager = \Yii::createObject(MediaManager::class);
        $mediaManager->saveForAccount($this->account, $posts);
    }
}