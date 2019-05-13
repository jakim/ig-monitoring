<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\components;


use app\components\traits\BatchInsertCommand;
use app\components\traits\FindOrCreate;
use app\models\Account;
use app\models\AccountTag;
use app\models\Media;
use app\models\MediaTag;
use app\models\Tag;
use app\models\User;
use DateTime;
use function is_string;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class TagManager extends Component
{
    use FindOrCreate, BatchInsertCommand;

    public function startMonitoring(string $name, $proxyId = null): Tag
    {
        /** @var Tag $tag */
        $tag = $this->findOrCreate(['name' => $name], Tag::class);

        $tag->proxy_id = $proxyId;
        $tag->monitoring = 1;
        $tag->disabled = 0;

        $tag->save();

        return $tag;
    }

    public function addToMedia(Media $media, array $tags)
    {
        $this->saveTags($tags);

        $rows = array_map(function ($id) use ($media) {
            return [
                $media->id,
                $id,
                $media->taken_at,
            ];
        }, Tag::find()
            ->andWhere(['name' => $tags])
            ->column());

        $this->batchInsertIgnoreCommand(MediaTag::tableName(), ['media_id', 'tag_id', 'created_at'], $rows)
            ->execute();
    }

    /**
     * Adds new ones.
     *
     * @param \app\models\Account $account
     * @param array|Tag[] $tags
     * @param int|null $userId
     * @throws \yii\db\Exception
     */
    public function addToAccount(Account $account, array $tags, ?int $userId = null)
    {
        $rows = [];
        $createdAt = (new DateTime())->format('Y-m-d H:i:s');
        $userIds = $this->getUserIds($userId);
        $tagIds = $this->getTagIds($tags);
        foreach ($userIds as $userId) {
            $tmp = array_map(function ($tagId) use ($account, $userId, $createdAt) {
                return [
                    $account->id,
                    $tagId,
                    $userId,
                    $createdAt,
                ];
            }, $tagIds);
            $rows = ArrayHelper::merge($rows, $tmp);
        }

        $this->batchInsertIgnoreCommand(AccountTag::tableName(), ['account_id', 'tag_id', 'user_id', 'created_at'], $rows)
            ->execute();
    }

    /**
     * It deletes the previous ones and sets new ones.
     *
     * @param \app\models\Account $account
     * @param array|Tag[] $tags
     * @param int|null $userId
     * @throws \yii\db\Exception
     */
    public function saveForAccount(Account $account, array $tags, ?int $userId = null)
    {
        $this->deleteAccountTags($account, $userId);
        if ($tags) {
            $this->addToAccount($account, $tags, $userId);
        }
    }

    /**
     * @param array|string[] $tags Names array
     * @throws \yii\db\Exception
     */
    public function saveTags(array $tags)
    {
        $createdAt = (new DateTime())->format('Y-m-d H:i:s');
        $rows = array_map(function ($tag) use ($createdAt) {
            return [
                $tag,
                Inflector::slug($tag),
                $createdAt,
                $createdAt,
            ];
        }, $tags);

        $this->batchInsertIgnoreCommand(Tag::tableName(), ['name', 'slug', 'updated_at', 'created_at'], $rows)
            ->execute();
    }

    /**
     * @param array $tags
     * @return array
     * @throws \yii\db\Exception
     */
    private function getTagIds(array $tags): array
    {
        if (is_string($tags['0'])) {
            $this->saveTags($tags);
        } else {
            $tags = ArrayHelper::getColumn($tags, 'name');
        }

        return Tag::find()
            ->andWhere(['name' => $tags])
            ->column();
    }

    /**
     * @param int $userId
     * @return array
     */
    private function getUserIds(?int $userId): array
    {
        if (!$userId) {
            $userIds = User::find()
                ->andWhere(['active' => 1])
                ->column();
        } else {
            $userIds = (array)$userId;
        }

        return $userIds;
    }

    private function deleteAccountTags(Account $account, ?int $userId)
    {
        if ($userId) {
            return AccountTag::deleteAll(['account_id' => $account->id, 'user_id' => $userId]);
        }

        return AccountTag::deleteAll(['account_id' => $account->id]);
    }
}