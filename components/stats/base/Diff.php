<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 21.06.2018
 */

namespace app\components\stats\base;


use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

abstract class Diff extends Component
{
    /**
     * @var \app\models\Account[]|\app\models\Tag[]
     */
    protected $models;

    protected $statsAttributes = [];

    protected $diffCache = [];
    protected $lastDiffCache = [];

    public function init()
    {
        parent::init();
        if ($this->models === null) {
            throw new InvalidConfigException('Property \'models\' must be set.');
        }
    }

    /**
     * @param null $id AccountId or TagId
     * @return array
     */
    public function getDiff($id = null)
    {
        return $this->getFromCache($this->diffCache, $id);
    }

    /**
     * @param null $id AccountId or TagId
     * @return array
     */
    public function getLastDiff($id = null)
    {
        return $this->getFromCache($this->lastDiffCache, $id);
    }

    /**
     * @param \app\models\Account[]|array $models
     * @return self
     */
    public function setModels($models)
    {
        $this->models = (array)$models;

        return $this;
    }

    protected function getModelIds()
    {
        return ArrayHelper::getColumn($this->models, 'id');
    }

    protected function getFromCache($cache, $accountId)
    {
        if ($accountId === null) {
            return $cache;
        }

        if ($accountId && isset($cache[$accountId])) {
            return $cache[$accountId];
        }

        return [];
    }

    protected function prepareCache($rawStats, $groups)
    {
        $cache = [];
        $rawStats = ArrayHelper::index($rawStats, null, $groups);

        foreach ($rawStats as $id => $stats) {
            $older = array_shift($stats);
            foreach ($stats as $newer) {
                foreach ($this->statsAttributes as $statsAttribute) {
                    $value = ArrayHelper::getValue($newer, $statsAttribute, 0) - ArrayHelper::getValue($older, $statsAttribute, 0);
                    $cache[$id][$newer['created_at']][$statsAttribute] = $value;
                }
                $older = $newer;
            }
        }

        return $cache;
    }
}