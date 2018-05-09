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
        if ($account && !$account->disabled) {


            try {

                $proxy = $this->reserveProxy($account);

                $manager = \Yii::createObject([
                    'class' => AccountManager::class,
                    'scraper' => [
                        'class' => AccountScraper::class,
                        'proxy' => $proxy,
                    ],
                ]);
                $manager->update($account);
                $this->releaseProxy($proxy);

            } catch (\Throwable $exception) {
                $this->releaseProxy($proxy);

                throw $exception;
            }

        }
    }

    protected function releaseProxy(Proxy $proxy)
    {
        Proxy::updateAll(['reservation_uid' => null], 'reservation_uid=:reservation_uid', [':reservation_uid' => $proxy->reservation_uid]);
    }

    protected function reserveProxy($account)
    {
        $uid = $this->generateUid();

        $sql = \Yii::$app->db->createCommand()
            ->update('proxy', [
                'reservation_uid' => $uid,
                'updated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ], [
                'and',
                ['reservation_uid' => null],
                ['<=', 'updated_at', (new \DateTime('-1 seconds'))->format('Y-m-d H:i:s')],
            ])->rawSql;

        $n = \Yii::$app->db->createCommand("{$sql} ORDER BY [[updated_at]] ASC LIMIT 1")
            ->execute();

        if (empty($n)) {
            throw new Exception('No proxy available.');
        }

        return Proxy::findOne(['reservation_uid' => $uid]);
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    protected function generateUid(): string
    {
        return sprintf("%s_%s", \Yii::$app->security->generateRandomString(64), time());
    }
}