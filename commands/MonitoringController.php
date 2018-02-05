<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\commands;


use app\dictionaries\ProxyType;
use app\models\Account;
use app\models\Proxy;
use app\models\Tag;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;
use yii\helpers\Inflector;

class MonitoringController extends Controller
{
    public function actionIndex()
    {
        $this->actionAccounts();
        $this->actionTags();
    }

    public function actionAccount($username, $proxy_id = null)
    {
        $account = Account::findOne(['username' => $username]);
        if ($account === null) {
            $account = new Account(['username' => $username]);
        }

        $proxy = Proxy::findOne(['id' => $proxy_id, 'type' => ProxyType::ACCOUNT]);
        if ($proxy) {
            $account->proxy_id = $proxy->id;

        } elseif ($proxy === null && $proxy_id) {
            $this->stdout("ERR: Proxy '$proxy_id' not found.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        } elseif (!$proxy_id && !Proxy::find()->andWhere(['type' => ProxyType::ACCOUNT])->exists()) {
            $this->stdout("ERR: There MUST be at least one proxy for accounts.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $account->monitoring = 1;
        if (!$account->save()) {
            print_r($account->errors);

            return ExitCode::UNSPECIFIED_ERROR;
        }
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    public function actionAccounts()
    {
        echo Table::widget([
            'headers' => [
                'ID',
                'Username',
            ],
            'rows' => Account::find()
                ->select([
                    'id',
                    'username',
                ])
                ->monitoring()
                ->asArray()
                ->all(),
        ]);
    }

    public function actionTag($name, $proxy_id = null)
    {
        $tag = Tag::findOne(['name' => $name]);
        if ($tag === null) {
            $tag = new Tag(['name' => $name]);
            $mainTag = Tag::findOne(['slug' => Inflector::slug($name)]);
            if ($mainTag) {
                $tag->main_tag_id = $mainTag->id;
            }
        }
        $proxy = Proxy::findOne(['id' => $proxy_id, 'type' => ProxyType::TAG]);
        if ($proxy) {
            $tag->proxy_id = $proxy->id;
        } elseif ($proxy === null && $proxy_id) {
            $this->stdout("ERR: Proxy '$proxy_id' not found.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        } elseif (!$proxy_id && !Proxy::find()->andWhere(['type' => ProxyType::TAG])->exists()) {
            $this->stdout("ERR: There MUST be at least one proxy for accounts.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $tag->monitoring = 1;
        if (!$tag->save()) {
            print_r($tag->errors);

            return ExitCode::UNSPECIFIED_ERROR;
        }
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    public function actionTags()
    {
        echo Table::widget([
            'headers' => [
                'ID',
                'Tag',
            ],
            'rows' => Tag::find()
                ->select([
                    'id',
                    'name',
                ])
                ->monitoring()
                ->asArray()
                ->all(),
        ]);
    }
}