<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2018-10-30
 */

namespace app\dictionaries;


abstract class ChartType
{
    const LINE = 'line';
    const BAR = 'bar';
    const HORIZONTAL_BAR = 'horizontalBar';
    const PIE = 'pie';
}