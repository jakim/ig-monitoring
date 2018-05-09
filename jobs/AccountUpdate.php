<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\jobs;


use app\components\AccountManager;
use app\components\instagram\AccountScraper;
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
        if ($account) {

            $uid = $this->reserveProxy($account);

            try {
                $proxy = Proxy::findOne(['reservation_uid' => $uid]);

                if (($diff = time() - strtotime($proxy->updated_at)) < 2) {
                    sleep($diff);
                }

                $manager = \Yii::createObject([
                    'class' => AccountManager::class,
                    'scraper' => [
                        'class' => AccountScraper::class,
                        'proxy' => $proxy,
                    ],
                ]);
                $manager->update($account);

            } catch (\Throwable $exception) {
                $this->releaseProxy($uid);

                throw $exception;
            }

            $this->releaseProxy($uid);
        }
    }

    /**
     * @param $uid
     */
    protected function releaseProxy($uid): void
    {
        Proxy::updateAll(['reservation_uid' => null], ['reservation_uid' => $uid]);
    }

    protected function reserveProxy($account): bool
    {
        $uid = uniqid(time(), true);

        $sql = 'UPDATE proxy set reservation_uid=:reservation_uid, updated_at=:updated_at WHERE reservation_uid IS NULL ORDER BY updated_at ASC LIMIT 1';

        $n = \Yii::$app->db->createCommand($sql, [
            'reservation_uid' => $uid,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ])->execute();

        if (empty($n)) {
            throw new Exception('No proxy available.');
        }

        return $uid;
    }
}