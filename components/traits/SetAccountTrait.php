<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-02-11
 */

namespace app\components\traits;


use app\models\Account;
use yii\base\InvalidConfigException;

trait SetAccountTrait
{
    /**
     * @var \app\models\Account
     */
    public $account;

    protected function throwExceptionIfAccountIsNotSet()
    {
        if (!$this->account instanceof Account) {
            throw new InvalidConfigException('Property \'account\' must be set and be type of \'\app\models\Account\'.');
        }
    }
}