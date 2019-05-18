<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-29
 */

namespace app\components\stats\contracts;


use Carbon\Carbon;

interface FromToDateInterface
{
    public function setFrom(Carbon $date);

    public function setTo(Carbon $date);
}