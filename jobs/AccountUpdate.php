<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\jobs;


use app\components\AccountManager;
use app\models\Account;
use yii\queue\JobInterface;
use yii\queue\Queue;

class AccountUpdate implements JobInterface
{
    public $id;

    /**
     * @param \yii\queue\Queue $queue
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        $account = Account::findOne($this->id);
        if ($account) {
            $manager = \Yii::createObject(AccountManager::class);
            $manager->update($account);
        }
    }
}