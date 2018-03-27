<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 13.01.2018
 */

namespace app\commands;


use app\models\Account;
use app\models\Tag;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\StringHelper;

class AccountController extends Controller
{
    /**
     * Update account usernames.
     * format: from1,to1;from2,to2;from3,to3
     */
    public function actionUpdateUsername($usernames)
    {
        $rows = StringHelper::explode($usernames, ';', true, true);
        foreach ($rows as $row) {
            $username = StringHelper::explode($row, ',', true, true);
            $account = Account::findOne(['username' => $username['0']]);
            if ($account) {
                $account->username = $username['1'];
                if (!$account->update()) {
                    echo Console::errorSummary($account);

                    return ExitCode::DATAERR;
                }

            }
        }
    }
}