<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\commands;


use app\models\Proxy;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;

class ProxyController extends Controller
{
    public function actionIndex()
    {
        $proxies = Proxy::find()
            ->orderBy('ip')
            ->all();
        $rows = [];
        foreach ($proxies as $proxy) {
            $rows[] = [
                $proxy->id,
                $proxy->ip,
                $proxy->port,
                $proxy->username,
                $proxy->default_for_accounts,
                $proxy->default_for_tags,
            ];
        }

        echo Table::widget([
            'headers' => ['ID', 'IP', 'Port', 'Username', 'Defaults for accounts', 'Default for tags'],
            'rows' => $rows,
        ]);
    }

    /**
     * NOTE: Moved to admin panel.
     *
     * @deprecated
     */
    public function actionCreate()
    {
        $this->stdout("moved to admin panel\n");

        return ExitCode::OK;
    }
}