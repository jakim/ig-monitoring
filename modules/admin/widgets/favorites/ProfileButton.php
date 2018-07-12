<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 02.02.2018
 */

namespace app\modules\admin\widgets\favorites;


use app\models\Account;
use app\models\Favorite;
use app\modules\admin\widgets\AjaxButton;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class ProfileButton extends Widget
{
    public $model;

    public $addLabel = '<span class="fa fa-star text-yellow"></span> Add to favorites';
    public $removeLabel = '<span class="fa fa-star-o text-yellow"></span> Remove from favorites';

    public function run()
    {
        $url = Url::to(['/admin/account/dashboard', 'id' => $this->model->id]);

        $model = Favorite::findOne([
            'user_id' => \Yii::$app->user->id,
            'url' => $url,
        ]);

        return AjaxButton::widget([
            'confirm' => isset($model),
            'text' => $model ? $this->removeLabel : $this->addLabel,
            'url' => $model ? ['favorite/delete', 'id' => $model->id] : ['favorite/create'],
            'data' => [
                'url' => $url,
                'label' => $this->model->usernamePrefixed,
            ],
            'options' => [
                'class' => 'btn btn-block btn-default btn-sm',
                'data' => [
                    'style' => 'zoom-out',
                ],
            ],
        ]);
    }
}