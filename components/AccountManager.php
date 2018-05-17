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
use yii\db\IntegrityException;
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
        $n = $details->updateMedia($account, $posts);
        // update account er
        $stats->updateEr($account, $n);
    }

    public function monitorMultiple(array $usernames, Account $parent)
    {
        $usernames = array_filter(array_unique($usernames));
        foreach ($usernames as $username) {
            $account = $this->monitor($username);
            $account->proxy_id = $parent->proxy_id;
            $account->proxy_tag_id = $parent->proxy_tag_id;

            //calculation monitoring level
            if ($parent->accounts_monitoring_level > 1) {
                $level = $parent->accounts_monitoring_level - 1;
                if ($level > $account->accounts_monitoring_level) {
                    $account->accounts_monitoring_level = $level;
                }
            }
            $account->save();

            $this->updateTags($account, $parent->tags);
        }
    }

    public function monitor(string $username, $proxyId = null, $proxyTagId = null): Account
    {
        $account = Account::findOne(['username' => $username]);
        if ($account === null) {
            $account = new Account(['username' => $username]);
        }

        $account->proxy_id = $proxyId;
        $account->proxy_tag_id = $proxyTagId;
        $account->monitoring = 1;

        $account->save();

        return $account;
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
        foreach ($tags as $tag) {
            if (is_string($tag)) {
                $name = $tag;
                $tag = Tag::findOne(['name' => $name]);
                if ($tag === null) {
                    $tag = new Tag(['name' => $name]);
                    $tag->insert();
                }
            }
            try {
                $account->link('tags', $tag);
            } catch (IntegrityException $exception) {
                continue;
            }
        }
    }
}