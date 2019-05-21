<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 18.06.2018
 */

namespace app\components\services;


use app\components\http\Client;
use app\components\http\ProxyManager;
use app\components\instagram\AccountScraper;
use app\components\instagram\models\Account;
use app\components\MediaManager;
use app\components\services\contracts\ServiceInterface;
use app\components\updaters\AccountUpdater;
use app\dictionaries\AccountInvalidationType;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Jakim\Exception\RestrictedProfileException;
use Yii;
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
        $proxyManager = Yii::createObject(ProxyManager::class);
        $accountUpdater = Yii::createObject([
            'class' => AccountUpdater::class,
            'account' => $this->account,
        ]);

        try {
            $proxy = $proxyManager->reserve($this->account);
            $httpClient = Client::factory($proxy, [], 3600);

            $scraper = Yii::createObject(AccountScraper::class, [
                $httpClient,
            ]);

            $accountData = $this->fetchAccountData($scraper);
            $posts = $scraper->fetchLastPosts($accountData->username);

            $proxyManager->release($proxy);
            unset($proxy);


            if ($accountData->isPrivate) {
                $accountUpdater
                    ->setIsInValid(AccountInvalidationType::IS_PRIVATE)
                    ->setNextStatsUpdate(true)
                    ->save();
            } else {
                $accountUpdater
                    ->setDetails($accountData)
                    ->setIdents($accountData)
                    ->setIsValid()
                    ->setStats($accountData, $posts)
                    ->setNextStatsUpdate()
                    ->save();

                $mediaManager = Yii::createObject(MediaManager::class);
                $mediaManager->addToAccount($this->account, $posts);

            }

        } catch (NotFoundHttpException $exception) {
            $accountUpdater
                ->setIsInValid(AccountInvalidationType::NOT_FOUND)
                ->setNextStatsUpdate(true)
                ->save();
        } catch (RestrictedProfileException $exception) {
            $accountUpdater
                ->setIsInValid(AccountInvalidationType::RESTRICTED_PROFILE)
                ->setNextStatsUpdate(true)
                ->save();
        } catch (RequestException $exception) {
            $accountUpdater
                ->setIsInValid()
                ->setNextStatsUpdate(true)
                ->save();
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
                Yii::error($exception->getMessage(), __METHOD__);
                continue;
            }
            break;
        }

        if (empty($accountData)) {
            throw new NotFoundHttpException();
        }

        return $accountData;
    }

}