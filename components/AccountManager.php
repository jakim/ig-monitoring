<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 28.04.2018
 */

namespace app\components;


use app\components\traits\BatchInsertCommand;
use app\components\traits\FindOrCreate;
use app\components\updaters\AccountUpdater;
use app\models\Account;
use app\models\AccountCategory;
use app\models\Media;
use app\models\MediaAccount;
use DateTime;
use Yii;
use yii\base\Component;
use yii\helpers\StringHelper;
use function is_string;

class AccountManager extends Component
{
    use FindOrCreate, BatchInsertCommand;

    public function startMonitoring($account, $proxyId = null): Account
    {
        if (is_string($account)) {
            /** @var Account $account */
            $account = $this->findOrCreate(['username' => $account], Account::class);
        }

        $accountUpdater = Yii::createObject([
            'class' => AccountUpdater::class,
            'account' => $account,
        ]);
        $accountUpdater
            ->setMonitoring($proxyId)
            ->setIsValid()
            ->save();

        return $account;
    }

    /**
     * @param \app\models\Account $parent
     * @param array $accounts
     *
     * @deprecated
     */
    public function monitorRelatedAccounts(Account $parent, array $accounts)
    {
        foreach ($accounts as $account) {
            if (is_string($account)) {
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

            $this->startMonitoring($account, $parent->proxy_id);
        }
    }

    public function addToMedia(Media $media, array $usernames)
    {
        $this->saveUsernames($usernames);

        $accounts = Account::find()
            ->andWhere(['username' => $usernames])
            ->column();

        $rows = array_map(function ($id) use ($media) {
            return [
                $media->id,
                $id,
                $media->taken_at,
            ];
        }, $accounts);

        $this->batchInsertIgnoreCommand(MediaAccount::tableName(), ['media_id', 'account_id', 'created_at'], $rows)
            ->execute();
    }

    /**
     * @param array|string[] $usernames
     * @throws \yii\db\Exception
     */
    public function saveUsernames(array $usernames)
    {
        $createdAt = (new DateTime())->format('Y-m-d H:i:s');
        $rows = array_map(function ($username) use ($createdAt) {
            return [
                $username,
                $createdAt,
                $createdAt,
            ];
        }, $usernames);

        $this->batchInsertIgnoreCommand(Account::tableName(), ['username', 'updated_at', 'created_at'], $rows)
            ->execute();
    }

    /**
     * @param string|string[] $categories
     * @param null|int $userId
     * @return array|int[]
     */
    public function findByCategories($categories, $userId = null): array
    {
        if (is_string($categories)) {
            $categories = StringHelper::explode($categories, ',', true, true);
            $categories = array_unique($categories);
        }

        $ids = [];
        foreach ($categories as $category) {
            $ids[] = AccountCategory::find()
                ->distinct()
                ->select('account_id')
                ->innerJoinWith('category')
                ->andFilterWhere(['user_id' => $userId])
                ->andFilterWhere(['like', 'category.name', $category])
                ->column();
        }

        return array_intersect(...$ids, ...$ids);
    }
}