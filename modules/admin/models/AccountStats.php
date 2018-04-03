<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 03.04.2018
 */

namespace app\modules\admin\models;


class AccountStats extends \app\models\AccountStats
{
    /**
     * @var string Y-m-d
     */
    public $day;

    /**
     * @var string Y-m
     */
    public $month;
}