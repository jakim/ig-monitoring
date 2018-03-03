<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.02.2018
 */

namespace app\modules\admin\widgets;


use yii\base\Widget;
use yii\helpers\Html;

class OnOffMonitoringButton extends Widget
{
    public $model;
    public $linkCssClass = 'btn btn-block';

    public function run()
    {
        echo Html::a($this->model->monitoring ? '<span class="fa fa-stop"></span> Turn off monitoring' : '<span class="fa fa-play"></span> Turn on monitoring', ['monitoring', 'id' => $this->model->id], [
            'class' => $this->linkCssClass . ($this->model->monitoring ? ' btn-danger' : ' btn-success'),
            'data' => [
                'method' => 'post',
                'confirm' => 'Are you sure?',
            ],
        ]);
    }
}