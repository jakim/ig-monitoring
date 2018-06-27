<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace app\components;


use app\components\traits\FindOrCreate;
use app\models\Account;
use app\models\Media;
use jakim\ig\Text;
use yii\base\Component;

class MediaManager extends Component
{
    use FindOrCreate;

    /**
     * @var \app\models\Account
     */
    public $account;

    /**
     * @param \app\models\Account $account
     * @param \app\components\instagram\models\Post[]
     * @throws \yii\base\InvalidConfigException
     */
    public function saveForAccount(Account $account, array $posts)
    {
        foreach ($posts as $post) {
            /** @var Media $media */
            $media = $this->findOrCreate([
                'account_id' => $account->id,
                'shortcode' => $post->shortcode,
            ], Media::class);
            /** @var \app\components\MediaUpdater $updater */
            $updater = \Yii::createObject([
                'class' => MediaUpdater::class,
                'media' => $media,
            ]);
            $updater->details($post);

            $this->saveRelatedData($media, $account);
        }
    }

    protected function saveRelatedData(Media $media, Account $account)
    {
        if (empty($media->caption)) {
            return false;
        }

        $tags = (array)Text::getTags($media->caption);
        if ($tags) {
            $manager = \Yii::createObject(TagManager::class);
            $manager->saveForMedia($media, $tags);
        }

        $usernames = (array)Text::getUsernames($media->caption);
        if ($usernames) {
            // ignore owner of media
            ArrayHelper::removeValue($usernames, $account->username);

            $manager = \Yii::createObject(AccountManager::class);
            $manager->saveForMedia($media, $usernames);

            if ($account && $account->accounts_monitoring_level > 0) {
                $manager->monitorRelatedAccounts($account, $usernames);
            }
        }

        return true;
    }
}