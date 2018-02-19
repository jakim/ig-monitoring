<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\commands;


use app\dictionaries\ProxyType;
use app\models\Proxy;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;

class ProxyController extends Controller
{
    public function actionIndex()
    {
        $proxies = Proxy::find()
            ->orderBy('type, ip')
            ->all();
        $rows = [];
        foreach ($proxies as $proxy) {
            $rows[] = [
                $proxy->id,
                $proxy->ip,
                $proxy->port,
                $proxy->username,
                $proxy->type,
            ];
        }

        echo Table::widget([
            'headers' => ['ID', 'IP', 'Port', 'Username', 'Type'],
            'rows' => $rows,
        ]);
    }

    public function actionCreate($ip, $port, $username = null, $password = null, $type = ProxyType::ACCOUNT)
    {
        $model = Proxy::findOne(['ip' => $ip, 'port' => $port]);
        if ($model === null) {
            $model = new Proxy(['ip' => $ip, 'port' => $port]);
        }
        $model->type = in_array($type, [ProxyType::ACCOUNT, ProxyType::MEDIA, ProxyType::TAG]) ? $type : ProxyType::ACCOUNT;
        $model->username = $username;
        $model->password = $password;
        $model->active = 1;
        if (!$model->save()) {
            echo Console::errorSummary($model);

            return ExitCode::DATAERR;
        }
        $this->stdout("OK!: ID = {$model->id}\n");

        return ExitCode::OK;
    }
}