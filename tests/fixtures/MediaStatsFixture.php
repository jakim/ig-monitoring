<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.05.2018
 */

namespace app\tests\fixtures;


use app\models\MediaStats;
use yii\test\ActiveFixture;

class MediaStatsFixture extends ActiveFixture
{
    public $modelClass = MediaStats::class;

    public function load()
    {
    }
}