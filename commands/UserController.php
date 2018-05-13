<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 05.02.2018
 */

namespace app\commands;


use app\models\User;
use app\modules\admin\components\AccountStatsManager;
use app\modules\admin\models\Account;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;

class UserController extends Controller
{

    public function actionTest()
    {
        $manager = \Yii::createObject([
            'class' => AccountStatsManager::class,
            'account' => Account::findOne(755),
        ]);

        print_r($manager->lastMonthChange('followed_by'));
    }

    /**
     *
     * @param mixed $ident ID or Email
     * @return int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionActivate($ident)
    {
        $user = User::findOne(is_numeric($ident) ? $ident : ['email' => $ident]);
        if ($user === null) {
            $this->stdout("User not found!\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }
        $user->active = 1;
        $user->update(false);
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    public function actionIndex()
    {
        echo Table::widget([
            'headers' => [
                'ID',
                'Username',
                'Email',
                'Active',
                'Access Token',
            ],
            'rows' => User::find()
                ->select([
                    'id',
                    'username',
                    'email',
                    'active',
                    'access_token',
                ])
                ->orderBy('id DESC')
                ->asArray()
                ->all(),
        ]);
    }
}