<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 29.04.2018
 */

namespace app\components\instagram;


use app\components\MediaManager;
use app\models\Account;
use app\models\Media;
use yii\base\Component;

class AccountDetails extends Component
{
    public function updateDetails(Account $account, \Jakim\Model\Account $data): Account
    {
        $account->username = $data->username;
        $account->full_name = $data->fullName;
        $account->biography = $data->biography;
        $account->external_url = $data->externalUrl;
        $account->instagram_id = (string) $data->id;

        $account->update();

        return $account;
    }

    /**
     * @param \app\models\Account $account
     * @param \Generator|\Jakim\Model\Post[] $items
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function updateMedia(Account $account, $items)
    {
        $manager = \Yii::createObject([
            'class' => MediaManager::class,
            'account' => $account,
        ]);

        foreach ($items as $item) {
            $media = Media::findOne(['instagram_id' => $item->id]);
            if ($media === null) {
                $media = new Media(['account_id' => $account->id]);
            }
            $manager->update($media, $item);
        }
    }

    public function profilePicNeedUpdate(Account $account, \Jakim\Model\Account $data): bool
    {
        $filename = $this->profilePicFilename($account, $data);
        $path = $this->profilePicPath($filename);

        return !file_exists($path);
    }

    public function updateProfilePic(Account $account, \Jakim\Model\Account $data, string $imageData): Account
    {
        $filename = $this->profilePicFilename($account, $data);
        $path = $this->profilePicPath($filename);

        if ($imageData && file_put_contents($path, $imageData)) {
            $account->profile_pic_url = "/uploads/{$filename}";
        }
        $account->update(true, ['profile_pic_url']);

        return $account;
    }

    /**
     * @param \app\models\Account $account
     * @param \Jakim\Model\Account $data
     * @return string
     */
    protected function profilePicFilename(Account $account, \Jakim\Model\Account $data): string
    {
        $filename = sprintf('%s_%s', $account->username, basename($data->profilePicUrl));

        return $filename;
    }

    /**
     * @param $filename
     * @return bool|string
     */
    protected function profilePicPath($filename)
    {
        $path = \Yii::getAlias("@app/web/uploads/{$filename}");

        return $path;
    }
}