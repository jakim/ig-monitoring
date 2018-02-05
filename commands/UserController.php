<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 05.02.2018
 */

namespace app\commands;


use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;

class UserController extends Controller
{

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
            ],
            'rows' => User::find()
                ->select([
                    'id',
                    'username',
                    'email',
                    'active',
                ])
                ->orderBy('id DESC')
                ->asArray()
                ->all(),
        ]);
    }
}