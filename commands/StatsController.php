<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\commands;


use app\components\AccountManager;
use app\components\instagram\AccountScraper;
use app\components\JobFactory;
use app\components\TagManager;
use app\models\Account;
use app\models\AccountStats;
use app\models\Tag;
use app\models\TagStats;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Expression;
use yii\helpers\Console;

class StatsController extends Controller
{
    /**
     * Create update jobs.
     *
     * @param int $force ignore interval
     * @param int $interval in hours
     * @return int
     */
    public function actionUpdateTags($force = 0, $interval = 24)
    {
        $query = Tag::find()
            ->select('id')
            ->monitoring();

        if (!$force) {
            $tagIds = TagStats::find()
                ->select('tag_id')
                ->andWhere($this->whereInterval($interval))
                ->column();
            $query->andFilterWhere(['not', ['id' => $tagIds]]);
        }

        /** @var \yii\queue\Queue $queue */
        $queue = \Yii::$app->queue;
        foreach ($query->column() as $tagId) {
            $queue->push(JobFactory::createTagUpdate($tagId));
        }
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    /**
     * Update on run.
     *
     * @param $name
     * @return int
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateTag($name)
    {
        $tag = Tag::findOne(['name' => $name]);
        if ($tag === null) {
            $this->stdout("Tag '$name' not found.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $manager = \Yii::createObject(TagManager::class);
        $manager->update($tag);
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    /**
     * Create update jobs.
     *
     * @param int $force ignore interval
     * @param int $interval in hours
     * @return int
     */
    public function actionUpdateAccounts($force = 0, $interval = 24)
    {
        $query = Account::find()
            ->select('id')
            ->monitoring();

        if (!$force) {
            $accountIds = AccountStats::find()
                ->select('account_id')
                ->andWhere($this->whereInterval($interval))
                ->column();
            $query->andFilterWhere(['not', ['id' => $accountIds]]);
        }

        /** @var \yii\queue\Queue $queue */
        $queue = \Yii::$app->queue;
        foreach ($query->column() as $accountId) {
            $queue->push(JobFactory::createAccountUpdate($accountId));
        }
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    /**
     * Update on run.
     *
     * @param $username
     * @return int
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateAccount($username)
    {
        $account = Account::findOne(['username' => $username]);
        if ($account === null) {
            $this->stdout("Account '$username' not found.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        /** @var AccountManager $manager */
        $manager = \Yii::createObject(AccountManager::class);
        $manager->update($account);
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    /**
     * @param $interval
     * @return Expression
     */
    private function whereInterval($interval): Expression
    {
        return new Expression('DATE_FORMAT(created_at, \'%Y-%m-%d %H\') > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL :interval HOUR), \'%Y-%m-%d %H\')', [
            'interval' => (int) $interval,
        ]);
    }
}