<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 28.06.2018
 */

namespace app\modules\admin\widgets;


use app\models\AccountStats;
use yii\base\Widget;

class NoStatsDataAlert extends Widget
{
    public $model;

    public function run()
    {
        $count = AccountStats::find()
            ->andWhere(['account_id' => $this->model->id])
            ->exists();
        if (!$count) {
            echo '<div class="callout callout-info">';
            echo '<p class="lead"><span class="fa fa-cog fa-spin"></span> Collecting data...</p>';
            echo '<p>Please come back tomorrow.</p>';
            echo '</div>';
        }
    }
}