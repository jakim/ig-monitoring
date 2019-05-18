<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-24
 */

namespace app\components\stats\traits;


use Carbon\Carbon;
use yii\base\InvalidConfigException;

trait FromToDateTrait
{
    /**
     * @var \Carbon\Carbon
     */
    protected $from;

    /**
     * @var \Carbon\Carbon
     */
    protected $to;

    public function setFrom(Carbon $date)
    {
        $this->from = $date;

        return $this;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getFrom(): Carbon
    {
        return $this->from;
    }

    public function setTo(Carbon $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getTo(): Carbon
    {
        return $this->to;
    }


    protected function throwExceptionIfFromToAreNotSet()
    {
        if (!$this->from || !$this->to) {
            throw new InvalidConfigException('Properties \'from\' and \'to\' can not be empty.');
        }
    }
}