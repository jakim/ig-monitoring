<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 01.02.2018
 */

namespace app\modules\admin\widgets\favorites;


use app\components\ArrayHelper;
use app\models\Favorite;
use app\modules\admin\widgets\AjaxButton;
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
        parent::init();
    }

    public function run()
    {
        $favorites = Favorite::find()
            ->andWhere([
                'user_id' => \Yii::$app->user->id,
            ])
            ->orderBy('label ASC')
            ->all();
        foreach ($favorites as $favorite) {
            $this->items[] = [
                'label' => $favorite->label,
                'url' => $favorite->url,
                'id' => $favorite->id,
            ];
        }
        if ($this->items) {
            array_unshift($this->items, ['label' => 'Favorites', 'options' => ['class' => 'header']]);
        }
        parent::run();
    }

    protected function isItemActive($item)
    {
        if (!isset($item['url'])) {
            return false;
        }

        if ($item['url'] == Url::current()) {
            return true;
        }
        if (isset($item['url'])) {
            $url = Url::to(["/admin/{$this->view->context->id}/dashboard", 'id' => \Yii::$app->request->get('id')]);

            return $item['url'] == $url;
        }

        return false;
    }

    protected function renderItem($item)
    {
        $tmp = $this->linkTemplate;
        $id = ArrayHelper::getValue($item, 'id');
        $btnDelete = '';
        if ($id) {
            $btnDelete = AjaxButton::widget([
                'confirm' => true,
                'tag' => 'span',
                'text' => '<i class="fa fa-trash-o pull-right delete"></i>',
                'url' => ['favorite/delete', 'id' => $id],
                'options' => [
                    'class' => 'pull-right-container',
                    'data' => [
                        'style' => 'slide-right',
                    ],
                ],
            ]);
        }

        $this->linkTemplate = str_replace('{btn-delete}', $btnDelete, $this->linkTemplate);

        $html = parent::renderItem($item);
        $this->linkTemplate = $tmp;

        return $html;
    }
}