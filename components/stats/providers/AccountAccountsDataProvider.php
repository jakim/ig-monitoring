<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2018-10-31
 */

namespace app\components\stats\providers;


use app\components\stats\contracts\DataProviderInterface;
use app\components\stats\traits\FromToDateTrait;
use app\components\stats\traits\StatsAttributesTrait;
use app\components\traits\SetAccountTrait;
use app\models\Account;
use yii\data\ActiveDataProvider;

class AccountAccountsDataProvider extends ActiveDataProvider implements DataProviderInterface
{
    use FromToDateTrait, SetAccountTrait, StatsAttributesTrait;

    public function init()
    {
        parent::init();
        $this->throwExceptionIfFromToAreNotSet();
        $this->throwExceptionIfAccountIsNotSet();

        $this->query = Account::find()
            ->select([
                'account.*',
                'count(account.id) as occurs',
            ])
            ->innerJoin('media_account', 'account.id=media_account.account_id')
            ->innerJoin('media', 'media_account.media_id=media.id')
            ->andWhere(['media.account_id' => $this->account->id])
            ->andWhere(['>=', 'media_account.created_at', $this->from->startOfDay()->toDateTimeString()])
            ->andWhere(['<=', 'media_account.created_at', $this->to->endOfDay()->toDateTimeString()])
            ->groupBy('account.id');

        $this->sort->attributes['occurs'] = [
            'asc' => ['occurs' => SORT_ASC],
            'desc' => ['occurs' => SORT_DESC],
        ];

        $this->sort->defaultOrder = [
            'occurs' => SORT_DESC,
        ];
    }
}