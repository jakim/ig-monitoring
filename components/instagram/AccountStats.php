<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 28.04.2018
 */

namespace app\components\instagram;


use app\models\Account;
use app\models\Media;
use yii\base\Component;
use app\models\AccountStats as AccountStatsModel;

class AccountStats extends Component
{
    public function updateStats(Account $account, \Jakim\Model\Account $data)
    {
        $accountStats = new AccountStatsModel();
        $accountStats->followed_by = $data->followedBy;
        $accountStats->follows = $data->follows;
        $accountStats->media = $data->media;
        $account->link('accountStats', $accountStats);
        $account->refresh();

        return $accountStats;
    }

    public function statsNeedUpdate(Account $account, \Jakim\Model\Account $data): bool
    {
        $account->refresh();
        if (!$account->lastAccountStats) {
            return true;
        }

        return $account->lastAccountStats->followed_by != $data->followedBy ||
            $account->lastAccountStats->follows != $data->follows ||
            $account->lastAccountStats->media != $data->media;
    }

    public function updateEr(Account $account)
    {
        $account->refresh();
        if (!$account->lastAccountStats) {
            return false;
        }

        $media = Media::find()
            ->andWhere(['account_id' => $account->id])
            ->orderBy('taken_at DESC')
            ->limit(10)
            ->all();

        $er = [];
        foreach ($media as $m) {
            if ($m->account_followed_by) {
                $er[] = ($m->likes + $m->comments) / $m->account_followed_by;
            }
        }

        $er = $er ? array_sum($er) / count($er) : 0;
        $account->lastAccountStats->er = round($er, 4);

        return $account->lastAccountStats->update();
    }
}