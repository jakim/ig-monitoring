<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 01.02.2018
 */

namespace app\modules\admin\widgets\favorites;


use app\components\AjaxButton;
use app\components\ArrayHelper;
use app\models\Favorite;
use dmstr\widgets\Menu;
use yii\helpers\Url;
use yii\web\JsExpression;

class SideMenu extends Menu
{
    public $encodeLabels = false;
    public $linkTemplate = '<a href="{url}">{icon} {label} {btn-delete}</a>';

    public function init()
    {
        $this->options = ['class' => 'sidebar-menu tree favorites', 'data-widget' => 'tree'];
        $this->defaultIconHtml = '<i class="fa fa-star"></i> ';
//        $this->view->registerJs('
//        jQuery(\'.favorites .delete\').click(function(e){
//            e.preventDefault();
//            var $el = jQuery(this);
//            var id = $el.attr(\'data-id\');
//            jQuery.ajax({
//                url: \'' . Url::to(['favorite/delete']) . '\',
//                data: {id: id},
//                success: function(){
//                    jQuery(\'.favorites\').find(\'li[data-id=\'+id+\']\').remove();
//                }
//            })
//        });
//        ');
        parent::init();
    }

    public function run()
    {
        $favorites = Favorite::find()
            ->orderBy('label ASC')
            ->all();
        foreach ($favorites as $favorite) {
            $this->items[] = [
                'label' => $favorite->label,
                'url' => $favorite->url,
                'options' => [
                    'data' => [
                        'id' => $favorite->id,
                    ],
                ],
            ];
        }
        if ($this->items) {
            array_unshift($this->items, ['label' => 'Favorites', 'options' => ['class' => 'header']]);
        }
        parent::run();
    }

    protected function isItemActive($item)
    {
        if (isset($item['url'])) {
            $url = Url::to(["/admin/{$this->view->context->id}/dashboard", 'id' => \Yii::$app->request->get('id')]);

            return $item['url'] == $url;
        }

        return false;
    }

    protected function renderItem($item)
    {
        $tmp = $this->linkTemplate;

        $id = ArrayHelper::getValue($item, 'options.data.id');
        if ($id) {
            $btnDelete = AjaxButton::widget([
                'tag' => 'span',
                'text' => '<i class="fa fa-trash-o pull-right delete"></i>',
                'confirm' => 'Are you sure?',
                'url' => ['favorite/delete', 'id' => $id],
                'data' => [
                    'id' => $id,
                ],
                'options' => [
                    'class' => 'pull-right-container',
                ],
                'successCallback' => new JsExpression(sprintf("function(){jQuery('.favorites').find('li[data-id=%s]').remove();}", $id)),
            ]);

            $this->linkTemplate = str_replace('{btn-delete}', $btnDelete, $this->linkTemplate);
        }

        $html = parent::renderItem($item);
        $this->linkTemplate = $tmp;

        return $html;
    }
}