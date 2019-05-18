<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\commands;


use app\components\JobFactory;
use app\models\Account;
use app\models\Tag;
use Yii;
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
     * @return int
     */
    public function actionUpdateTags($force = 0)
    {
        $query = Tag::find()
            ->select('id')
            ->monitoring();

        if (!$force) {
            if (!$force) {
                $query->andWhere(['or',
                    $this->updateAfterExpression(),
                    ['update_stats_after' => null],
                ]);
            }
        }

        /** @var \yii\queue\Queue $queue */
        $queue = Yii::$app->queue;
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
     */
    public function actionUpdateTag($name)
    {
        $tag = Tag::findOne(['name' => $name]);
        if ($tag === null) {
            $this->stdout("Tag '$name' not found.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        /** @var \yii\queue\Queue $queue */
        $queue = Yii::$app->queue;
        $queue->push(JobFactory::createTagUpdate($tag->id));
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    /**
     * Create update jobs.
     *
     * @param int $force ignore interval
     * @return int
     */
    public function actionUpdateAccounts($force = 0)
    {
        $query = Account::find()
            ->select('id')
            ->monitoring();

        if (!$force) {
            $query->andWhere(['or',
                $this->updateAfterExpression(),
                ['update_stats_after' => null],
            ]);
        }

        /** @var \yii\queue\Queue $queue */
        $queue = Yii::$app->queue;
        foreach ($query->column() as $accountId) {
            $queue->push(JobFactory::createAccountUpdate($accountId));
        }
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    public function actionUpdateAccount($username)
    {
        $account = Account::findOne(['username' => $username]);
        if ($account === null) {
            $this->stdout("Account '$username' not found.\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        /** @var \yii\queue\Queue $queue */
        $queue = Yii::$app->queue;
        $queue->push(JobFactory::createAccountUpdate($account->id));
        $this->stdout("OK!\n");

        return ExitCode::OK;
    }

    /**
     * @return Expression
     */
    protected function updateAfterExpression(): Expression
    {
        return new Expression('DATE_FORMAT(update_stats_after, \'%Y-%m-%d %H\') < DATE_FORMAT(NOW(), \'%Y-%m-%d %H\')');
    }
}