<?php
/**
 * Created for monitoring-free.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-05-24
 */

namespace app\commands;


use app\components\AccountManager;
use app\components\TagManager;
use app\models\Account;
use app\models\AccountCategory;
use app\models\AccountStats;
use app\models\Favorite;
use app\models\Media;
use app\models\Tag;
use app\models\TagStats;
use app\models\User;
use Yii;
use yii\console\Controller;
use yii\db\Expression;

class DemoController extends Controller
{
    public function actionActivateUsers()
    {
        User::updateAll(['active' => 1]);
    }

    public function actionCleanup()
    {
        $this->resetMonitoring();
        $this->cleanDb();
    }

    public function cleanDb()
    {
        AccountStats::deleteAll([
            'account_id' => Account::find()
                ->select('id')
                ->andWhere(['monitoring' => 0]),
        ]);
        AccountStats::deleteAll(['<', 'created_at', new Expression('DATE_SUB(NOW(), INTERVAL 2 MONTH)')]);

        TagStats::deleteAll([
            'tag_id' => Tag::find()
                ->select('id')
                ->andWhere(['monitoring' => 0]),
        ]);
        TagStats::deleteAll(['<', 'created_at', new Expression('DATE_SUB(NOW(), INTERVAL 2 MONTH)')]);

        Media::deleteAll(['<', 'created_at', new Expression('DATE_SUB(NOW(), INTERVAL 2 MONTH)')]);
        Favorite::deleteAll();
        AccountCategory::deleteAll();
    }

    protected function resetMonitoring()
    {
        $accounts = ['instagram', 'natgeo', 'unicef'];
        $tags = ['nature', 'outdoor', 'cats', 'dogs'];

        $accountManager = Yii::createObject(AccountManager::class);
        $tagManager = Yii::createObject(TagManager::class);

        foreach ($accounts as $account) {
            $accountManager->startMonitoring($account);
        }

        foreach ($tags as $tag) {
            $tagManager->startMonitoring($tag);
        }

        Account::updateAll(['monitoring' => 0], ['NOT', ['username' => $accounts]]);
        Tag::updateAll(['monitoring' => 0], ['NOT', ['name' => $tags]]);
    }
}