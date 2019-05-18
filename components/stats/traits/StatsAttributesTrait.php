<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-25
 */

namespace app\components\stats\traits;


use yii\base\InvalidConfigException;

/**
 * Trait StatsAttributesTrait
 *
 * @package app\components\stats\traits
 *
 * @property array $statsAttributes
 */
trait StatsAttributesTrait
{
    protected $statsAttributes = [];

    public function setStatsAttributes(array $statsAttributes)
    {
        $this->statsAttributes = $statsAttributes;

        return $this;
    }

    public function getStatsAttributes(): array
    {
        return $this->statsAttributes;
    }

    protected function throwExceptionIfStatsAttributesIsNotSet()
    {
        if (!$this->statsAttributes) {
            throw new InvalidConfigException('Property \'statsAttributes\' can not be empty.');
        }
    }
}