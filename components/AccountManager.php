<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 28.04.2018
 */

namespace app\components;


use app\components\instagram\AccountDetails;
use app\components\instagram\AccountScraper;
use app\components\instagram\AccountStats;
use app\models\Account;
use app\models\AccountTag;
use app\models\Tag;
use yii\base\Component;
use yii\web\NotFoundHttpException;

class AccountManager extends Component
{
    public $scraper = AccountScraper::class;

    /**
     * Fetch data from API, update details and stats.
     *
     * @param \app\models\Account $account
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function update(Account $account)
    {
        /** @var AccountScraper $scraper */
        $scraper = \Yii::createObject($this->scraper);
        /** @var AccountDetails $details */
        $details = \Yii::createObject(AccountDetails::class);
        /** @var AccountStats $stats */
        $stats = \Yii::createObject(AccountStats::class);

        try {
            $data = $scraper->fetchDetails($account);

        } catch (NotFoundHttpException $exception) {

            $account->disabled = 1;
            $account->update(false);

            throw $exception;
        } catch (\Throwable $exception) {
            throw $exception;
        }

        // update account details
        $details->updateDetails($account, $data);

        // update profile pic
        if ($details->profilePicNeedUpdate($account, $data)) {
            $content = $scraper->fetchProfilePic($account, $data->profilePicUrl);
            $details->updateProfilePic($account, $data, $content);
        }

        // update account stats
        if ($stats->statsNeedUpdate($account, $data)) {
            $stats->updateStats($account, $data);
        }

        $posts = $scraper->fetchMedia($account);
        // update account media
        $details->updateMedia($account, $posts);
        // update account er
        $stats->updateEr($account);
    }

    public function saveUsernames(array $usernames)
    {
        $createdAt = (new \DateTime())->format('Y-m-d H:i:s');
        $rows = array_map(function ($username) use ($createdAt) {
            return [
                $username,
                $createdAt,
                $createdAt,
            ];
        }, $usernames);

        $sql = \Yii::$app->db->getQueryBuilder()
            ->batchInsert(Account::tableName(), ['username', 'updated_at', 'created_at'], $rows);
        $sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);
        \Yii::$app->db->createCommand($sql)
            ->execute();
    }

    public function updateTags(Account $account, array $tags)
    {
        // clearing
        AccountTag::deleteAll(['account_id' => $account->id]);

        // add
        foreach (array_filter($tags) as $tag) {
            $model = Tag::findOne(['name' => $tag]);
            if ($model === null && $tag) {
                $model = new Tag(['name' => $tag]);
                $model->insert();
            }
            $account->link('tags', $model);
        }
    }
}