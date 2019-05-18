<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 13.01.2018
 */

namespace app\commands;


use app\models\Account;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\StringHelper;

class AccountController extends Controller
{
    public function resetDisabled()
    {
        Account::updateAll(['disabled' => 0]);
    }

    /**
     * Update account usernames.
     * format: username1_from,username1_to1,name1;username2_from,username2_to,name2;username3_from,username3_to,name3
     *
     * @param $names
     * @return int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUpdateName($names)
    {
        $rows = StringHelper::explode($names, ';', true, true);
        foreach ($rows as $row) {
            $username = StringHelper::explode($row);
            if (count($username) != 3) {
                $this->stdout("Wrong format!\n", Console::BG_RED);
            }
            $account = Account::findOne(['username' => $username['0']]);
            if ($account) {
                if ($username['1']) {
                    $account->username = $username['1'];
                }
                if ($username['2']) {
                    $account->name = $username['2'];
                }
                if (!$account->update()) {
                    echo Console::errorSummary($account);

                    return ExitCode::DATAERR;
                }

            }
        }
    }
}