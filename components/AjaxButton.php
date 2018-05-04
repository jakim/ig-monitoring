<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 04.05.2018
 */

namespace app\components;


use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class AjaxButton extends Widget
{
    public $tag = 'a';
    public $text;
    public $url;
    public $options = ['class' => 'btn btn-link'];
    public $method = 'post';
    public $data = [];
    public $confirm;
    public $successCallback;

    public function run()
    {
        $options = $this->options;
        if (!isset($options['id'])) {
            $options['id'] = $this->getId();
        }

        echo Html::tag($this->tag, $this->text, $options);

        $this->registerClientScripts();
    }

    protected function registerClientScripts()
    {
        $options = [
            'url' => Url::to($this->url),
            'method' => $this->method,
            'data' => $this->data,
            'success' => $this->successCallback,
        ];
        $options = Json::encode($options);

        $js = <<<JS
jQuery('#{$this->getId()}').click(function(e){
    e.preventDefault();
    if ("{$this->confirm}"){
        if(confirm("{$this->confirm}")){
            jQuery.ajax($options);         
        }
    }else{
        jQuery.ajax($options);
    } 
});
JS;
        $this->view->registerJs($js);
    }
}