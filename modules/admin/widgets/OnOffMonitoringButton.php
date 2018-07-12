<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.02.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use app\dictionaries\TrackerType;
use app\models\Tag;
use app\modules\admin\models\MonitoringForm;
use yii\base\Widget;
use yii\helpers\Url;

class OnOffMonitoringButton extends Widget
{
    /**
     * @var \app\models\Tag|\app\models\Account
     */
    public $model;
    public $form;
    public $btnCssClass = 'btn btn-block';
    public $stopBtnCssClass = 'btn-danger';
    public $startBtnCssClass = 'btn-success';
    public $stopBtnLabel = '<span class="fa fa-stop"></span> Turn off monitoring';
    public $startBtnLabel = '<span class="fa fa-play"></span> Turn on monitoring';

    public $offAjaxOptions = [];

    protected $trackerType = TrackerType::ACCOUNT;

    public function init()
    {
        parent::init();
        if ($this->model instanceof Tag) {
            $this->trackerType = TrackerType::TAG;
        } else {
            $this->trackerType = TrackerType::ACCOUNT;
        }
    }

    public function run()
    {
        if ($this->model->monitoring) {
            return AjaxButton::widget([
                'confirm' => true,
                'url' => Url::to(["monitoring/delete-{$this->trackerType}", 'id' => $this->model->id]),
                'options' => [
                    'class' => "{$this->btnCssClass} {$this->stopBtnCssClass}",
                    'data' => [
                        'style' => 'slide-right',
                    ],
                ],
                'text' => $this->stopBtnLabel,
                'ajaxOptions' => $this->offAjaxOptions,
            ]);
        }

        $form = $this->form ?: new MonitoringForm([
            'scenario' => $this->trackerType,
            'names' => ArrayHelper::getValue($this->model, $this->trackerType == TrackerType::ACCOUNT ? 'username' : 'name'),
            'proxy_id' => $this->model->proxy_id,
            'proxy_tag_id' => $this->model->proxy_tag_id,

            'tags' => $this->trackerType == TrackerType::ACCOUNT ? ArrayHelper::getColumn($this->model->tags, 'name') : null,
        ]);

        return CreateMonitoringModal::widget([
            'form' => $form,
            'trackerType' => $this->trackerType,
            'modalToggleButton' => [
                'class' => "{$this->btnCssClass} {$this->startBtnCssClass}",
                'label' => $this->startBtnLabel,
            ],
        ]);
    }
}