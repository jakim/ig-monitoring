<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-25
 */

namespace app\components\stats\contracts;


interface DiffInterface extends FromToDateInterface
{
    public function getData();
}