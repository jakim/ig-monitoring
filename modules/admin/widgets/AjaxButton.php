<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 10.07.2018
 */

namespace app\modules\admin\widgets;


use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

class AjaxButton extends Widget
{
    public $confirm = false;
    public $confirmMessage = 'Are you sure?';
    public $successCallback;
    public $data = [];

    public $text = 'Update';
    public $url;

    public $tag = 'a';
    public $options = [
        'class' => 'btn btn-success',
        'data' => [
            'style' => 'zoom-out',
        ],
    ];
    public $ajaxOptions = [];

    public function init()
    {
        parent::init();

        $this->confirm = Json::encode($this->confirm);
        $this->successCallback = $this->successCallback ?: new JsExpression('function(data){if(typeof data === \'string\' || data instanceof String){window.location.replace(data);}else{location.reload();}}');

        $this->setId('ab-' . $this->getId());
        $this->view->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.6/ladda-themeless.min.css');
        $this->view->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.6/spin.min.js');
        $this->view->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.6/ladda.min.js');
    }

    public function run()
    {
        $options = $this->options;
        if (!isset($options['id'])) {
            $options['id'] = $this->getId();
        }
        Html::addCssClass($options, 'ladda-button');

        echo Html::tag($this->tag, $this->text, $options);

        $this->registerClientScript();
    }

    protected function registerClientScript()
    {
        $options = ArrayHelper::merge([
            'url' => Url::to($this->url),
            'method' => 'post',
            'data' => $this->data,
            'success' => $this->successCallback,
        ], $this->ajaxOptions);
        $options = Json::encode($options);

        $r = <<<JS
var l = Ladda.create(this);
l.start();
jQuery.ajax($options)
.always(function() { l.stop(); });         
JS;

        $js = <<<JS
jQuery('#{$this->getId()}').click(function(e){
    e.preventDefault();
    if ({$this->confirm}){
        if(confirm("{$this->confirmMessage}")){$r}
    }else{
        $r
    } 
});
JS;
        $this->view->registerJs($js);
    }
}