<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-26
 */

namespace app\components\stats\providers;


use app\components\stats\contracts\DataProviderInterface;
use app\components\stats\traits\FromToDateTrait;
use app\components\stats\traits\StatsAttributesTrait;
use app\components\traits\SetAccountTrait;
use app\models\Tag;
use yii\data\ActiveDataProvider;

class AccountTagsDataProvider extends ActiveDataProvider implements DataProviderInterface
{
    use SetAccountTrait, FromToDateTrait, StatsAttributesTrait;

    public function init()
    {
        parent::init();
        $this->throwExceptionIfAccountIsNotSet();
        $this->throwExceptionIfFromToAreNotSet();

        $this->query = Tag::find()
            ->select([
                'tag.*',
                'count(tag.id) as occurs',
                'AVG(media.likes) as ts_avg_likes',
            ])
            ->innerJoin('media_tag', 'tag.id=media_tag.tag_id')
            ->innerJoin('media', 'media_tag.media_id=media.id')
            ->andWhere(['media.account_id' => $this->account->id])
            ->andWhere(['>=', 'media_tag.created_at', $this->from->startOfDay()->toDateTimeString()])
            ->andWhere(['<=', 'media_tag.created_at', $this->to->endOfDay()->toDateTimeString()])
            ->groupBy('tag.id');

        $this->sort->attributes['occurs'] = [
            'asc' => ['occurs' => SORT_ASC],
            'desc' => ['occurs' => SORT_DESC],
        ];

        $this->sort->attributes['ts_avg_likes'] = [
            'asc' => ['ts_avg_likes' => SORT_ASC],
            'desc' => ['ts_avg_likes' => SORT_DESC],
        ];

        $this->sort->defaultOrder = [
            'occurs' => SORT_DESC,
        ];
    }
}