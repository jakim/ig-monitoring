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
use app\models\Tag;
use yii\data\ActiveDataProvider;

class TagTagsDataProvider extends ActiveDataProvider implements DataProviderInterface
{
    use FromToDateTrait, SetTagTrait, StatsAttributesTrait;

    public function init()
    {
        parent::init();
        $this->throwExceptionIfFromToAreNotSet();
        $this->throwExceptionIfTagIsNotSet();

        $this->query = Tag::find()
            ->select([
                'tag.*',
                'count(tag.id) as occurs',
                'AVG(media.likes) as ts_avg_likes',
            ])
            ->innerJoin('media_tag', 'tag.id=media_tag.tag_id')
            ->innerJoin('media', 'media_tag.media_id=media.id')
            ->innerJoin('media_tag mt', 'media_tag.media_id=mt.media_id')
            ->andWhere([
                'mt.top_post' => 1,
                'mt.tag_id' => $this->tag->id,
            ])
            ->andWhere(['>=', 'mt.created_at', $this->from->startOfDay()->toDateTimeString()])
            ->andWhere(['<=', 'mt.created_at', $this->to->endOfDay()->toDateTimeString()])
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