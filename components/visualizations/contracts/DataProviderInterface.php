<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-26
 */

namespace app\components\visualizations\contracts;


interface DataProviderInterface
{
    public function labels();

    public function dataSets();

    public function scales();
}