<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 02.02.2018
 */

namespace app\modules\admin\widgets;


use app\models\Account;
use app\models\Favorite;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class FavoriteButton extends Widget
{
    public $model;

    public function run()
    {
        echo Html::a(
            $this->isFavorite() ?
                '<span class="fa fa-star-o text-yellow"></span> Remove from favorites' :
                '<span class="fa fa-star text-yellow"></span> Add to favorites',
            ['favorite', 'id' => $this->model->id],
            [
                'class' => 'btn btn-block btn-default btn-sm',
                'data' => [
                    'method' => 'post',
                    'confirm' => 'Are you sure?',
                    'params' => [
                        'url' => $this->getUrl(),
                        'label' => $this->getLabel(),
                    ],
                ],
            ]);
    }

    private function isFavorite()
    {
        return Favorite::find()
            ->andWhere(['url' => $this->getUrl()])
            ->exists();
    }

    /**
     * @return mixed
     */
    protected function getLabel()
    {
        if ($this->model instanceof Account) {
            return $this->model->usernamePrefixed;
        }

        return 'no label';
    }

    /**
     * @return mixed|string
     */
    protected function getUrl()
    {
        return Url::to(["/admin/{$this->view->context->id}/stats", 'id' => $this->model->id]);
    }
}