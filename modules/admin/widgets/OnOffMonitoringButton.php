<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.02.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use app\models\Tag;
use app\modules\admin\models\AccountMonitoringForm;
use app\modules\admin\models\TagMonitoringForm;
use yii\base\Widget;
use yii\helpers\Html;

class OnOffMonitoringButton extends Widget
{
    public $model;
    public $form;
    public $linkCssClass = 'btn btn-block';
    public $stopLinkCssClass = 'btn-danger';
    public $startLinkCssClass = 'btn-success';

    public function run()
    {
        if ($this->model->monitoring) {
            echo Html::a('<span class="fa fa-stop"></span> Turn off monitoring', ['monitoring', 'id' => $this->model->id], [
                'class' => "{$this->linkCssClass} {$this->stopLinkCssClass}",
                'data' => [
                    'method' => 'post',
                    'confirm' => 'Are you sure?',
                ],
            ]);
        } elseif ($this->model instanceof Tag) {
            $form = $this->form ?: new TagMonitoringForm([
                'names' => $this->model->name,
                'proxy_id' => $this->model->proxy_id,
                'proxy_tag_id' => $this->model->proxy_tag_id,
            ]);
            echo CreateMonitoringModal::widget([
                'form' => $form,
                'title' => 'Tags',
                'formAction' => ['monitoring/create-tag'],
                'modalToggleButton' => [
                    'class' => "{$this->linkCssClass} {$this->startLinkCssClass}",
                    'label' => '<span class="fa fa-play"></span> Turn on monitoring',
                ],
            ]);
        } else {
            $form = $this->form ?: new AccountMonitoringForm([
                'names' => $this->model->username,
                'tags' => ArrayHelper::getColumn($this->model->tags, 'name'),
                'proxy_id' => $this->model->proxy_id,
                'proxy_tag_id' => $this->model->proxy_tag_id,
            ]);
            echo CreateMonitoringModal::widget([
                'form' => $form,
                'modalToggleButton' => [
                    'class' => "{$this->linkCssClass} {$this->startLinkCssClass}",
                    'label' => '<span class="fa fa-play"></span> Turn on monitoring',
                ],
            ]);
        }
    }
}