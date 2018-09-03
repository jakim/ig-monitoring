<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.06.2018
 */

namespace app\components;


use app\components\instagram\models\Account;
use app\components\traits\FindOrCreate;
use app\models\AccountStats;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class AccountUpdater extends Component
{
    use FindOrCreate;

    /**
     * @var \app\models\Account
     */
    public $account;

    public function init()
    {
        parent::init();
        if (!$this->account instanceof \app\models\Account) {
            throw new InvalidConfigException('Property \'account\' must be set and be type od \'\app\models\Account\'.');
        }
    }

    public function details(Account $account): \app\models\Account
    {
        $this->account->username = $account->username;
        $this->account->instagram_id = (string)$account->id;
        $this->account->profile_pic_url = $account->profilePicUrl;
        $this->account->full_name = $account->fullName;
        $this->account->biography = $account->biography;
        $this->account->external_url = $account->externalUrl;

        $this->saveModel($this->account);

        return $this->account;
    }

    public function stats(Account $account): AccountStats
    {
        $accountStats = $this->account->lastAccountStats;
        if ($accountStats === null || $this->statsNeedUpdate($accountStats, $account)) {
            $accountStats = new AccountStats([
                'account_id' => $this->account->id,
                'media' => $account->media,
                'follows' => $account->follows,
                'followed_by' => $account->followedBy,
            ]);
        }
        $this->saveModel($accountStats);

        return $accountStats;
    }

    /**
     * @param \app\models\AccountStats $accountStats
     * @param \app\components\instagram\models\Post[]|\Generator $posts
     * @return \app\models\AccountStats
     * @throws \yii\web\ServerErrorHttpException
     */
    public function er(AccountStats $accountStats, $posts): AccountStats
    {
        $er = [];
        foreach ($posts as $post) {
            $er[] = ($post->likes + $post->comments) / $accountStats->followed_by;
        }

        $er = $er ? array_sum($er) / \count($er) : 0;
        $accountStats->er = round($er, 4);
        $this->saveModel($accountStats);

        return $accountStats;
    }

    private function statsNeedUpdate(AccountStats $accountStats, Account $account)
    {
        return $accountStats->media != $account->media ||
            $accountStats->follows != $account->follows ||
            $accountStats->followed_by != $account->followedBy;
    }

    private function saveModel(ActiveRecord $model)
    {
        if (!$model->save()) {
            throw new ServerErrorHttpException(sprintf('Validation: %s', json_encode($model->errors)));
        }

        return true;
    }
}