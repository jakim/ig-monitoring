<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\components;


use app\components\traits\FindOrCreate;
use app\models\Account;
use app\models\AccountTag;
use app\models\Media;
use app\models\MediaTag;
use app\models\Tag;
use app\models\User;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class TagManager extends Component
{
    use FindOrCreate;

    public function monitor(string $name, $proxyId = null, $proxyTagId = null): Tag
    {
        /** @var Tag $tag */
        $tag = $this->findOrCreate(['name' => $name], Tag::class);

        $tag->proxy_id = $proxyId;
        $tag->proxy_tag_id = $proxyTagId;
        $tag->monitoring = 1;

        $tag->save();

        return $tag;
    }

    public function saveForMedia(Media $media, array $tags)
    {
        $this->saveTags($tags);

        $createdAt = (new \DateTime())->format('Y-m-d H:i:s');
        $rows = array_map(function ($id) use ($media, $createdAt) {
            return [
                $media->id,
                $id,
                $createdAt,
            ];
        }, Tag::find()
            ->andWhere(['name' => $tags])
            ->column());

        $sql = \Yii::$app->db->queryBuilder
            ->batchInsert(MediaTag::tableName(), ['media_id', 'tag_id', 'created_at'], $rows);
        $sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);
        \Yii::$app->db->createCommand($sql)
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
    public function setForAccount(Account $account, array $tags, ?int $userId = null)
    {
        $this->deleteAccountTags($account, $userId);
        if ($tags) {
            $this->saveForAccount($account, $tags, $userId);
        }
    }

    /**
     * Adds new ones.
     *
     * @param \app\models\Account $account
     * @param array|Tag[] $tags
     * @param int|null $userId
     * @throws \yii\db\Exception
     */
    public function saveForAccount(Account $account, array $tags, ?int $userId = null)
    {
        $rows = [];
        $createdAt = (new \DateTime())->format('Y-m-d H:i:s');
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

        $sql = \Yii::$app->db->queryBuilder
            ->batchInsert(AccountTag::tableName(), ['account_id', 'tag_id', 'user_id', 'created_at'], $rows);
        $sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);
        \Yii::$app->db->createCommand($sql)
            ->execute();
    }

    /**
     * @param array|string[] $tags Names array
     * @throws \yii\db\Exception
     */
    public function saveTags(array $tags)
    {
        $createdAt = (new \DateTime())->format('Y-m-d H:i:s');
        $rows = array_map(function ($tag) use ($createdAt) {
            return [
                $tag,
                Inflector::slug($tag),
                $createdAt,
                $createdAt,
            ];
        }, $tags);

        $sql = \Yii::$app->db->getQueryBuilder()
            ->batchInsert(Tag::tableName(), ['name', 'slug', 'updated_at', 'created_at'], $rows);
        $sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);
        \Yii::$app->db->createCommand($sql)
            ->execute();
    }

    /**
     * @param array $tags
     * @return array
     * @throws \yii\db\Exception
     */
    private function getTagIds(array $tags): array
    {
        if (\is_string($tags['0'])) {
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
    private function getUserIds(int $userId): array
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