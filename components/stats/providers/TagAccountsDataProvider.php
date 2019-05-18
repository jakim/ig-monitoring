<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-29
 */

namespace app\components\stats\providers;


use app\components\stats\contracts\DataProviderInterface;
use app\components\stats\traits\FromToDateTrait;
use app\components\stats\traits\SetTagTrait;
use app\components\stats\traits\StatsAttributesTrait;
use app\models\Account;
use yii\data\ActiveDataProvider;

class TagAccountsDataProvider extends ActiveDataProvider implements DataProviderInterface
{
    use FromToDateTrait, SetTagTrait, StatsAttributesTrait;

    public function init()
    {
        parent::init();
        $this->throwExceptionIfFromToAreNotSet();
        $this->throwExceptionIfTagIsNotSet();

        $this->query = Account::find()
            ->select([
                'account.*',
                'count(account.id) as occurs',
            ])
            ->innerJoin('media', 'account.id=media.account_id')
            ->innerJoin('media_tag', 'media.id=media_tag.media_id')
            ->andWhere([
                'media_tag.tag_id' => $this->tag->id,
                'media_tag.top_post' => 1,
            ])
            ->andWhere(['>=', 'media.taken_at', $this->from->startOfDay()->toDateTimeString()])
            ->andWhere(['<=', 'media.taken_at', $this->to->endOfDay()->toDateTimeString()])
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