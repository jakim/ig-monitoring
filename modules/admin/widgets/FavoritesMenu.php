<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 01.02.2018
 */

namespace app\modules\admin\widgets;


use app\models\Favorite;
use dmstr\widgets\Menu;
use yii\helpers\Url;

class FavoritesMenu extends Menu
{
    public function init()
    {
        $this->options = ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'];
        $this->defaultIconHtml = '<i class="fa fa-star"></i> ';
        parent::init();
    }

    public function run()
    {
        $favorites = Favorite::find()
            ->orderBy('id DESC')
            ->all();
        foreach ($favorites as $favorite) {
            $this->items[] = [
                'label' => $favorite->label,
                'url' => $favorite->url,
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
            $url = Url::to(["/admin/{$this->view->context->id}/stats", 'id' => \Yii::$app->request->get('id')]);

            return $item['url'] == $url;
        }

        return false;
    }
}