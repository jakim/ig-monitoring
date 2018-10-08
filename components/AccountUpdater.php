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

    private $postStats;

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

    /**
     * Direct account statistics.
     *
     * @param \app\components\instagram\models\Account $account
     * @param array $posts
     * @return \app\models\AccountStats|null
     * @throws \yii\web\ServerErrorHttpException
     */
    public function stats(Account $account, array $posts): ?AccountStats
    {
        $accountStats = null;
        $this->account->touch('stats_updated_at');

        if ($this->account->media === null || $this->statsNeedUpdate($account, $posts)) {
            $postsStats = $this->postsStats($account, $posts);
            $this->account->media = $account->media;
            $this->account->follows = $account->follows;
            $this->account->followed_by = $account->followedBy;
            $this->account->er = $postsStats['er'];
            $this->account->avg_likes = $postsStats['avg_likes'];
            $this->account->avg_comments = $postsStats['avg_comments'];

            $accountStats = $this->createHistory();

            return $accountStats;
        }
        $this->saveModel($this->account);
    }

    protected function createHistory()
    {
        $accountStats = new AccountStats([
            'account_id' => $this->account->id,
            'media' => $this->account->media,
            'follows' => $this->account->follows,
            'followed_by' => $this->account->followed_by,
            'er' => $this->account->er,
            'avg_likes' => $this->account->avg_likes,
            'avg_comments' => $this->account->avg_comments,
        ]);
        $this->saveModel($accountStats);

        return $accountStats;
    }

    private function statsNeedUpdate(Account $account, array $posts): bool
    {
        $res = $this->account->media != $account->media ||
            $this->account->follows != $account->follows ||
            $this->account->followed_by != $account->followedBy;
        if ($res === false) {
            $postStats = $this->postsStats($account, $posts);
            $res = $this->account->er != $postStats['er'] ||
                $this->account->avg_likes != $postStats['avg_likes'] ||
                $this->account->avg_comments != $postStats['avg_comments'];
        }

        return $res;
    }

    /**
     * Statistics calculated from posts (engagement, avg_likes, avg_comments).
     *
     * @param \app\components\instagram\models\Account $account
     * @param array $posts
     * @return array
     */
    private function postsStats(Account $account, array $posts): array
    {
        if (!empty($this->postStats)) {
            return $this->postStats;
        }

        $er = [];
        if ($account->followedBy) {
            foreach ($posts as $post) {
                $er[] = ($post->likes + $post->comments) / $account->followedBy;
            }
        }
        $er = $er ? array_sum($er) / \count($er) : 0;

        $avgLikes = [];
        $avgComments = [];
        foreach ($posts as $post) {
            $avgLikes[] = $post->likes;
            $avgComments[] = $post->comments;
        }
        $avgLikes = $avgLikes ? array_sum($avgLikes) / \count($avgLikes) : 0;
        $avgComments = $avgComments ? array_sum($avgComments) / \count($avgComments) : 0;

        return $this->postStats = [
            'er' => $er,
            'avg_likes' => $avgLikes,
            'avg_comments' => $avgComments,
        ];
    }

    private function saveModel(ActiveRecord $model)
    {
        if (!$model->save()) {
            throw new ServerErrorHttpException(sprintf('Validation \'%s\': %s', get_class($model), json_encode($model->errors)));
        }

        return true;
    }
}