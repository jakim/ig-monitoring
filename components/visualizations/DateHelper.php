<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2018-09-27
 */

namespace app\components\visualizations;


use app\components\ArrayHelper;
use Carbon\Carbon;
use yii\helpers\StringHelper;

class DateHelper
{
    public static $dateFormat = 'd.m.Y';
    public static $rangeSeparator = ' - ';

    public static function getDefaultRange()
    {
        return sprintf('%s%s%s',
            Carbon::now()->startOfMonth()->format(static::$dateFormat),
            static::$rangeSeparator,
            Carbon::now()->endOfMonth()->format(static::$dateFormat)
        );
    }

    public static function getRangeFromUrl(string $getParamName = 'date_range')
    {
        $dateRangeDefaultValue = static::getDefaultRange();

        return \Yii::$app->request->get($getParamName, $dateRangeDefaultValue);
    }

    /**
     * @param string $dateRange
     * @return array|[Carbon, Carbon]  [start, end]
     */
    public static function normalizeRange(string $dateRange)
    {
        $dateRange = StringHelper::explode($dateRange, static::$rangeSeparator, true);
        $start = ArrayHelper::getValue($dateRange, '0');
        if ($start) {
            $start = Carbon::createFromFormat(static::$dateFormat, $start)->startOfDay();
        }
        $end = ArrayHelper::getValue($dateRange, '1');
        if ($end) {
            $end = Carbon::createFromFormat(static::$dateFormat, $end)->endOfDay();
        }

        return [$start, $end];
    }
}