<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\jobs;


use app\components\AccountManager;
use app\components\instagram\AccountScraper;
use app\components\ProxyManager;
use app\models\Account;
use app\models\Proxy;
use yii\base\Exception;
use yii\queue\JobInterface;
use yii\queue\Queue;

class AccountUpdate implements JobInterface
{
    public $id;

    /**
     * @param \yii\queue\Queue $queue
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function execute($queue)
    {
        $account = Account::findOne($this->id);
        if ($account && !$account->disabled) {

            $proxyManager = \Yii::createObject(ProxyManager::class);

            try {

                $proxy = $proxyManager->reserve($account);

                $manager = \Yii::createObject([
                    'class' => AccountManager::class,
                    'scraper' => [
                        'class' => AccountScraper::class,
                        'proxy' => $proxy,
                    ],
                ]);
                $manager->update($account);
                $proxyManager->release($proxy);

            } catch (\Throwable $exception) {
                
                if (isset($proxy)) {
                    $proxyManager->release($proxy);
                }

                throw $exception;
            }

        }
    }
}