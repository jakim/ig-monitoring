<?php /** @noinspection ALL */

/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\jobs;


use app\components\services\AccountUpdater;
use app\models\Account;
use Yii;
use yii\queue\JobInterface;

class UpdateAccountJob implements JobInterface
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

            $service = Yii::createObject([
                'class' => AccountUpdater::class,
                'account' => $account,
            ]);
            $service->run();
        }
    }
}