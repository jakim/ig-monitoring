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

        if (!$this->checkProxy($account, $proxy_id)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $account->monitoring = 1;
        if (!$account->save()) {
            echo Console::errorSummary($account);

            return ExitCode::DATAERR;
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
                'Proxy ID',
            ],
            'rows' => Account::find()
                ->select([
                    'id',
                    'username',
                    'proxy_id',
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

        if (!$this->checkProxy($tag, $proxy_id)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $tag->monitoring = 1;
        if (!$tag->save()) {
            echo Console::errorSummary($tag);

            return ExitCode::DATAERR;
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
                'Proxy ID',
            ],
            'rows' => Tag::find()
                ->select([
                    'id',
                    'name',
                    'proxy_id',
                ])
                ->monitoring()
                ->asArray()
                ->all(),
        ]);
    }

    /**
     * @param Account|Tag $model
     * @param $proxy_id
     * @return bool
     */
    private function checkProxy($model, $proxy_id)
    {
        $proxy = Proxy::findOne(['id' => $proxy_id, 'type' => $model->proxyType()]);

        if ($proxy === null && $proxy_id) {
            $this->stdout("ERR: Proxy '$proxy_id' not found.\n", Console::FG_RED);

            return false;

        } elseif (!$proxy_id && !Proxy::find()->andWhere(['type' => $model->proxyType()])->exists()) {
            $this->stdout("ERR: There MUST be at least one valid proxy.\n", Console::FG_RED);

            return false;
        } elseif ($proxy) {
            $model->proxy_id = $proxy->id;
        }

        return true;
    }
}