<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\commands;


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

    /**
     * NOTE: Moved to admin panel.
     *
     * @deprecated
     */
    public function actionAccount()
    {
        $this->stdout("moved to admin panel\n");

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
        $proxy = Proxy::findOne(['id' => $proxy_id]);

        if ($proxy_id && $proxy === null) {
            $this->stdout("ERR: Proxy '$proxy_id' not found.\n", Console::FG_RED);

            return false;

        }

        if (!$proxy && !$model->getProxy()) {
            $this->stdout("ERR: There MUST be at least one valid proxy.\n", Console::FG_RED);

            return false;
        }

        $model->proxy_id = $proxy ? $proxy->id : null;

        return true;
    }
}