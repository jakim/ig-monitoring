<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 28.04.2018
 */

namespace app\components;


use app\components\traits\FindOrCreate;
use app\models\Account;
use app\models\AccountTag;
use app\models\Media;
use app\models\MediaAccount;
use yii\base\Component;
use yii\db\Expression;
use yii\helpers\StringHelper;

class AccountManager extends Component
{
    use FindOrCreate;

    /**
     * @param \app\models\Account $account
     * @param int $nextUpdateInterval In hours from now.
     */
    public function markAsValid(Account $account, int $nextUpdateInterval = 24)
    {
        $account->invalidation_count = 0;
        $account->is_valid = 1;
        $account->invalidation_type_id = null;

        $this->setDateOfNextStatsUpdate($account, $nextUpdateInterval);
    }

    public function updateInvalidation(Account $account, ?int $invalidationType)
    {
        $account->invalidation_count = (int)$account->invalidation_count + 1;
        $account->is_valid = 0;
        $account->invalidation_type_id = $invalidationType;
        $interval = 1;
        for ($i = 1; $i <= $account->invalidation_count; $i++) {
            $interval *= $i;
        }
        $this->setDateOfNextStatsUpdate($account, $interval);
    }

    /**
     * @param \app\models\Account $account
     * @param int $interval In hours from now.
     */
    public function setDateOfNextStatsUpdate(Account $account, int $interval = 24)
    {
        $account->update_stats_after = new Expression('DATE_ADD(NOW(), INTERVAL :interval HOUR)', [
            'interval' => $interval,
        ]);
        $account->save();
    }

    public function monitorRelatedAccounts(Account $parent, array $accounts)
    {
        foreach ($accounts as $account) {
            if (\is_string($account)) {
                /** @var Account $account */
                $account = $this->findOrCreate(['username' => $account], Account::class);
                if ($account->disabled) {
                    continue;
                }
            }
            //calculation monitoring level
            if ($parent->accounts_monitoring_level > 1) {
                $level = $parent->accounts_monitoring_level - 1;
                if ($level > $account->accounts_monitoring_level) {
                    $account->accounts_monitoring_level = $level;
                }
            }

            $this->monitor($account, $parent->proxy_id, $parent->proxy_tag_id);

            $tags = empty($parent->accounts_default_tags) ? $parent->tags : StringHelper::explode($parent->accounts_default_tags, ',', true, true);
            $tagManager = \Yii::createObject(TagManager::class);
            $tagManager->saveForAccount($account, $tags);
        }
    }

    public function monitor($account, $proxyId = null, $proxyTagId = null): Account
    {
        if (\is_string($account)) {
            /** @var Account $account */
            $account = $this->findOrCreate(['username' => $account], Account::class);
        }

        $account->proxy_id = $proxyId;
        $account->proxy_tag_id = $proxyTagId;
        $account->monitoring = 1;

        $account->save();

        return $account;
    }

    public function saveForMedia(Media $media, array $usernames)
    {
        $this->saveUsernames($usernames);

        $rows = array_map(function ($id) use ($media) {
            return [
                $media->id,
                $id,
                $media->taken_at,
            ];
        }, Account::find()
            ->andWhere(['username' => $usernames])
            ->column());

        $sql = \Yii::$app->db->queryBuilder
            ->batchInsert(MediaAccount::tableName(), ['media_id', 'account_id', 'created_at'], $rows);
        $sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);
        \Yii::$app->db->createCommand($sql)
            ->execute();
    }

    /**
     * @param array|string[] $usernames
     * @throws \yii\db\Exception
     */
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

    /**
     * @param string|string[] $tags
     * @param null|int $userId
     * @return array|int[]
     */
    public function findByTags($tags, $userId = null): array
    {
        if (is_string($tags)) {
            $tags = StringHelper::explode($tags, ',', true, true);
            $tags = array_unique($tags);
        }

        $ids = [];
        foreach ($tags as $tag) {
            $ids[] = AccountTag::find()
                ->distinct()
                ->select('account_id')
                ->innerJoinWith('tag')
                ->andFilterWhere(['user_id' => $userId])
                ->andFilterWhere(['like', 'tag.name', $tag])
                ->column();
        }

        return array_intersect(...$ids, ...$ids);
    }
}