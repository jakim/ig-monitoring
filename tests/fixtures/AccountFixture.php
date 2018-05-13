<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.05.2018
 */

namespace app\tests\fixtures;


use app\modules\api\v1\models\Account;
use yii\test\ActiveFixture;

class AccountFixture extends ActiveFixture
{
    public $modelClass = Account::class;
}