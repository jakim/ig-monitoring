<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.06.2018
 */

namespace app\components\updaters;


use app\components\instagram\models\Account;
use app\components\traits\NextUpdateCalculatorTrait;
use app\components\traits\SaveModelTrait;
use app\components\traits\SetAccountTrait;
use app\models\AccountStats;
use DateTime;
use Throwable;
use yii\base\Component;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use function count;

class AccountUpdater extends Component
{
    use SaveModelTrait, SetAccountTrait, NextUpdateCalculatorTrait;

    private $postStats;

    public function init()
    {
        parent::init();
        $this->throwExceptionIfAccountIsNotSet();
    }

    /**
     * Update username and instagram_id.
     *
     * @param \app\components\instagram\models\Account $account
     * @return $this
     */
    public function setIdents(Account $account)
    {
        $this->account->username = $account->username;
        $this->account->instagram_id = (string)$account->id;

        return $this;
    }

    /**
     * @param \app\components\instagram\models\Account $account
     * @return $this
     */
    public function setDetails(Account $account)
    {
        $this->setIdents($account);
        $this->account->profile_pic_url = $account->profilePicUrl;
        $this->account->full_name = $account->fullName;
        $this->account->biography = $account->biography;
        $this->account->external_url = $account->externalUrl;
        $this->account->is_verified = $account->isVerified;
        $this->account->is_business = $account->isBusiness;
        $this->account->business_category = $account->businessCategory;

        return $this;
    }

    public function setMonitoring($proxyId = null)
    {
        $this->account->monitoring = 1;
        $this->account->proxy_id = $proxyId;

        return $this;
    }

    public function setIsValid()
    {
        $this->account->is_valid = 1;
        $this->account->invalidation_count = 0;
        $this->account->invalidation_type_id = null;

        return $this;
    }

    public function setIsInvalid(?int $invalidationType = null)
    {
        $this->account->is_valid = 0;
        $this->account->invalidation_count = (int)$this->account->invalidation_count + 1;
        $this->account->invalidation_type_id = $invalidationType;

        return $this;
    }

    public function setIsInvalidUnknown(string $message = null)
    {
        $this->account->is_valid = 0;
        $this->account->invalidation_count = (int)$this->account->invalidation_count + 1;
        $this->account->last_invalidation_unknown = $message;

        return $this;
    }

    /**
     * If true, then will be automatically calculate from invalidation_count
     *
     * @param true|int|null $interval
     * @return $this
     */
    public function setNextStatsUpdate($interval = 24)
    {
        $this->account->update_stats_after = $this->getNextUpdateDate($this->account, $interval);

        return $this;
    }

    /**
     * Direct account statistics.
     *
     * @param \app\components\instagram\models\Account $account
     * @param array|\app\components\instagram\models\Post[] $posts
     * @param bool $createHistory
     * @return $this
     */
    public function setStats(Account $account, array $posts, bool $createHistory = true)
    {
        $this->account->touch('stats_updated_at');
        if ($this->account->media === null || $this->statsNeedUpdate($account, $posts)) {
            $postsStats = $this->postsStats($account, $posts);
            $this->account->media = $account->media;
            $this->account->follows = $account->follows;
            $this->account->followed_by = $account->followedBy;
            $this->account->er = $postsStats['er'];
            $this->account->avg_likes = $postsStats['avg_likes'];
            $this->account->avg_comments = $postsStats['avg_comments'];

            if ($lastPost = ArrayHelper::getValue($posts, '0')) {
                try {
                    $takenAt = (new DateTime('@' . ArrayHelper::getValue($lastPost, 'takenAt')))->format('Y-m-d H:i:s');
                    $this->account->last_post_taken_at = $takenAt;
                } catch (Throwable $e) {
                }

            }

            if ($createHistory) {
                $this->createHistory();
            }
        }

        return $this;
    }

    /**
     * @throws \yii\web\ServerErrorHttpException
     */
    public function save()
    {
        $this->saveModel($this->account);
    }

    protected function createHistory()
    {
        // dirty fix - avoiding duplicates
        // the possibility of occurrence if the account is monitored and appears as a tagged/mentioned in the post
        AccountStats::deleteAll(['AND',
            ['account_id' => $this->account->id],
            new Expression('DATE(created_at)=DATE(NOW())'),
        ]);
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
     * @param array|\app\components\instagram\models\Post[] $posts
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
        $er = $er ? array_sum($er) / count($er) : 0;

        $avgLikes = [];
        $avgComments = [];
        foreach ($posts as $post) {
            $avgLikes[] = $post->likes;
            $avgComments[] = $post->comments;
        }
        $avgLikes = $avgLikes ? array_sum($avgLikes) / count($avgLikes) : 0;
        $avgComments = $avgComments ? array_sum($avgComments) / count($avgComments) : 0;

        return $this->postStats = [
            'er' => $er,
            'avg_likes' => $avgLikes,
            'avg_comments' => $avgComments,
        ];
    }
}