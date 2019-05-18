<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-29
 */

namespace app\components\stats\diffs;


use app\components\stats\base\BaseDiff;
use app\components\stats\traits\FromToDateTrait;
use app\components\stats\traits\StatsAttributesTrait;
use app\components\traits\SetTagTrait;
use app\models\TagStats;
use yii\base\InvalidConfigException;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class MultipleTagsDiff extends BaseDiff
{
    use FromToDateTrait, SetTagTrait, StatsAttributesTrait;

    /**
     * @var \app\models\Tag[]|array
     */
    public $tags;

    protected $tagIds = [];

    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function init()
    {
        parent::init();
        if ($this->tags === null) {
            throw new InvalidConfigException('Property \'tags\' can not be empty.');
        }
        $this->tagIds = ArrayHelper::getColumn($this->tags, 'id');
        $this->throwExceptionIfFromToAreNotSet();
        $this->throwExceptionIfStatsAttributesIsNotSet();
    }

    protected function prepareData(): array
    {
        $models = [];

        $dbTo = $this->to->copy()->endOfDay()->toDateTimeString();
        $dbFrom = $this->from->copy()->endOfDay()->toDateTimeString();

        $toStatsIds = $this->findStatsIds($dbTo);
        $toModels = $this->findDataModels($toStatsIds);

        // jesli znaleziona ostatnia data 'do' jest wczesniejsza niz od, to nie pomijaj modelu
        $ignoredModels = array_filter($toModels, function ($toModel) use ($dbFrom) {
            if (strtotime($toModel['created_at']) > strtotime($dbFrom)) {
                return true;
            }
        });

        $fromStatsIds = $this->findStatsIds($dbFrom, ArrayHelper::getColumn($ignoredModels, 'id'));
        $fromModels = $this->findDataModels($fromStatsIds);

        foreach ($toModels as $accountId => $toModel) {
            $fromModel = ArrayHelper::getValue($fromModels, $accountId);
            foreach ($this->statsAttributes as $statsAttribute) {
                $value = ArrayHelper::getValue($toModel, $statsAttribute, 0) - ArrayHelper::getValue($fromModel, $statsAttribute, 0);
                $models[$accountId][$statsAttribute] = $value;
            }
        }

        return $models;
    }

    protected function findStatsIds(?string $date, array $ignoredStatsIds = [])
    {
        return TagStats::find()
            ->cache()
            ->select(new Expression('MAX(id) as id'))
            ->indexBy('tag_id')
            ->andWhere(['tag_id' => $this->tagIds])
            ->andWhere(['<=', 'created_at', $date])
            ->andFilterWhere(['not', ['id' => $ignoredStatsIds]])
            ->groupBy('tag_id')
            ->column();
    }

    protected function findDataModels(array $statsIds)
    {
        $columns = array_map(function ($attr) {
            return "tag_stats.{$attr}";
        }, $this->statsAttributes);
        $columns[] = 'id';
        $columns[] = 'tag_id';
        $columns[] = new Expression('DATE(created_at) as created_at');

        return TagStats::find()
            ->cache()
            ->select($columns)
            ->indexBy('tag_id')
            ->andWhere(['id' => $statsIds])
            ->asArray()
            ->all();
    }
}