<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 01.02.2018
 */

namespace app\modules\admin\widgets\favorites;


use app\components\ArrayHelper;
use app\models\Favorite;
use dmstr\widgets\Menu;
use yii\helpers\Url;

class SideMenu extends Menu
{
    public $encodeLabels = false;
    public $linkTemplate = '<a href="{url}">{icon} {label}<span class="pull-right-container"><i class="fa fa-trash-o pull-right delete" data-id="{id}"></i></span></a>';

    public function init()
    {
        $this->options = ['class' => 'sidebar-menu tree favorites', 'data-widget' => 'tree'];
        $this->defaultIconHtml = '<i class="fa fa-star"></i> ';
        $this->view->registerJs('
        jQuery(\'.favorites .delete\').click(function(e){
            e.preventDefault();
            var $el = jQuery(this);
            var id = $el.attr(\'data-id\'); 
            jQuery.ajax({
                url: \'' . Url::to(['favorite/delete']) . '\',
                data: {id: id},
                success: function(){
                    jQuery(\'.favorites\').find(\'li[data-id=\'+id+\']\').remove();
                }
            })
        });
        ');
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
        $id = ArrayHelper::getValue($item, 'options.data.id');
        $tmp = $this->linkTemplate;
        $this->linkTemplate = str_replace('{id}', $id, $this->linkTemplate);
        $html = parent::renderItem($item);
        $this->linkTemplate = $tmp;

        return $html;
    }
}