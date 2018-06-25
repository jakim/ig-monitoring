<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\jobs;


use app\components\services\AccountFullUpdate;
use app\models\Account;
use yii\queue\JobInterface;

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

            $service = \Yii::createObject([
                'class' => AccountFullUpdate::class,
                'account' => $account,
            ]);
            $service->run();
        }
    }
}