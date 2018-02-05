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
use yii\helpers\Console;

class AccountController extends Controller
{
    /**
     * Tag accounts.
     *
     * @param array $usernames
     * @param array $tags
     */
    public function actionTag(array $usernames, array $tags)
    {
        $accounts = Account::findAll(['username' => $usernames]);
        $tags = Tag::findAll(['name' => $tags]);

        $this->stdout(sprintf("Valid accounts: %d, valid tags: %d\n", count($accounts), count($tags)));

        foreach ($accounts as $account) {
            foreach ($tags as $tag) {
                $account->link('tags', $tag);
            }
            $this->stdout("Account '{$account->username}' done!\n", Console::FG_GREEN);
        }
    }
}