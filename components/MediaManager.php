<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\components;


use app\models\Account;
use app\models\Media;
use app\models\MediaAccount;
use app\models\MediaStats;
use app\models\MediaTag;
use app\models\Tag;
use jakim\ig\Text;
use yii\base\Component;
use yii\base\Exception;

class MediaManager extends Component
{
    const PROPERTY_MAP_ACCOUNT_DETAILS = [
        'shortcode' => 'code',
        'is_video' => 'is_video',
        'caption' => 'caption',
        'instagram_id' => 'id',
        'taken_at' => 'date',
    ];

    const PROPERTY_MAP_ACCOUNT_MEDIA = [
        'shortcode' => 'node.shortcode',
        'is_video' => 'node.is_video',
        'caption' => 'node.edge_media_to_caption.edges.0.node.text',
        'instagram_id' => 'node.id',
        'taken_at' => 'node.taken_at_timestamp',
    ];

    /**
     * @var \app\models\Account
     */
    public $account;

    public $propertyMap = self::PROPERTY_MAP_ACCOUNT_DETAILS;

    /**
     * @param \app\models\Media $media
     * @param array $content Media node from account json
     * @return \app\models\MediaStats|null
     * @throws \yii\base\Exception
     */
    public function update(Media $media, array $content): ?MediaStats
    {
        $media = $this->updateDetails($media, $content);

        if (!$this->account) {
            $this->account = $media->account;
            $this->account->refresh();
        }

        if ($media->caption) {
            $tags = Text::getTags($media->caption);
            $this->addTags($media, $tags);

            $usernames = Text::getAccounts($media->caption);
            ArrayHelper::removeValue($usernames, $this->account->username);
            $this->addAccounts($media, $usernames);
        }

        return $this->updateStats($media, $content);
    }

    /**
     * @param \app\models\Media $media
     * @param array $content Media node from account json
     * @return \app\models\Media
     * @throws \yii\base\Exception
     */
    public function updateDetails(Media $media, array $content): Media
    {
        $mediaData = ArrayHelper::arrayMap($content, $this->propertyMap);
        $mediaData['taken_at'] = (new \DateTime('@' . $mediaData['taken_at']))->format('Y-m-d H:i:s');

        $media->attributes = $mediaData;

        if (!$media->account_id && $this->account) {
            $media->account_id = $this->account->id;
        }

        if (!$media->save()) {
            throw new Exception(json_encode($media->errors));
        }

        return $media;
    }

    /**
     * @param \app\models\Media $media
     * @param array $content Media node from account json
     * @return \app\models\MediaStats|null
     */
    public function updateStats(Media $media, array $content): ?MediaStats
    {
        $account = $media->account ?? $this->account;
        if (empty($account->lastAccountStats)) {
            return null;
        }

        $statsData = ArrayHelper::arrayMap($content, [
            'likes' => 'likes.count',
            'comments' => 'comments.count',
            'account_followed_by' => function () {
                return $this->account->lastAccountStats->followed_by;
            },
            'account_follows' => function () {
                return $this->account->lastAccountStats->follows;
            },
        ]);

        if (
            $media->lastMediaStats &&
            $media->lastMediaStats->likes == $statsData['likes'] &&
            $media->lastMediaStats->comments == $statsData['comments']
        ) {
            return null;
        }

        $mediaStats = new MediaStats();
        $mediaStats->attributes = $statsData;
        $media->link('mediaStats', $mediaStats);

        return $mediaStats;
    }

    public function addAccounts(Media $media, array $usernames)
    {
        $manager = \Yii::createObject(AccountManager::class);
        $manager->saveUsernames($usernames);

        $createdAt = (new \DateTime())->format('Y-m-d H:i:s');
        $rows = array_map(function ($id) use ($media, $createdAt) {
            return [
                $media->id,
                $id,
                $createdAt,
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
     * @param \app\models\Media $media
     * @param array $tags
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function addTags(Media $media, array $tags)
    {
        $manager = \Yii::createObject(TagManager::class);
        $manager->saveTags($tags);

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
}