<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 09.02.2018
 */

namespace app\commands;


use app\components\AccountManager;
use app\models\Account;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class HistoryController extends Controller
{
    public function actionAccount($username)
    {
        $account = Account::findOne(['username' => $username]);
        if ($account === null) {
            $this->stdout("Account '{$username}' not found.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }
        if (!$account->instagram_id) {
            $this->stdout("First update the account details (instagram Id is needed).\n", Console::FG_CYAN);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $manager = \Yii::createObject(AccountManager::class);
        $manager->updateMediaHistory($account);
    }
}