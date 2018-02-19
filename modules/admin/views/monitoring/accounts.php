<?php

use app\components\jakim\ig\Url;
use app\modules\admin\components\grid\AccountStatsColumn;
use app\modules\admin\models\Account;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\AccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Monitoring :: Accounts';
$this->params['breadcrumbs'][] = 'Monitoring';
$this->params['breadcrumbs'][] = 'Accounts';

/** @var \app\components\Formatter $formatter */
$formatter = Yii::$app->formatter;
?>
<div class="account-index nav-tabs-custom">

    <?= $this->render('_tabs') ?>

    <div class="tab-content table-responsive">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => \yii\grid\SerialColumn::class],
                [
                    'attribute' => 'username',
                    'content' => function (\app\models\Account $model) {
                        return Html::a($model->usernamePrefixed, ['account/dashboard', 'id' => $model->id]) . ' '
                            . Html::a('<span class="fa fa-external-link text-sm"></span>', Url::account($model->username), ['target' => '_blank']);
                    },
                ],
                [
                    'class' => AccountStatsColumn::class,
                    'attribute' => 'as_followed_by',
                    'statsAttribute' => 'followed_by',
                ],
                [
                    'class' => AccountStatsColumn::class,
                    'attribute' => 'as_follows',
                    'statsAttribute' => 'follows',
                ],
                [
                    'class' => AccountStatsColumn::class,
                    'attribute' => 'as_media',
                    'statsAttribute' => 'media',
                ],
                [
                    'class' => AccountStatsColumn::class,
                    'attribute' => 'as_er',
                    'statsAttribute' => 'er',
                    'numberFormat' => ['percent', 2, ['sign' => false]],
                ],
                [
                    'attribute' => 's_tags',
                    'value' => function (Account $model) {
                        $tags = $model->getTags()->select('tag.name')->column();
                        if ($tags) {
                            return implode(', ', $tags);
                        }
                    },
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                    'enableSorting' => false,
                ],
            ],
        ]); ?>

        <?= \app\modules\admin\widgets\CreateMonitoringWidget::widget() ?>

    </div>
</div>
