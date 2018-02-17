<?php

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
                        return Html::a($model->usernamePrefixed, ['account/stats', 'id' => $model->id]);
                    },
                ],
                [
                    'class' => AccountStatsColumn::class,
                    'statsAttribute' => 'followed_by',
                    'attribute' => 'as_followed_by',
                ],
                [
                    'class' => AccountStatsColumn::class,
                    'statsAttribute' => 'follows',
                    'attribute' => 'as_follows',
                ],
                [
                    'class' => AccountStatsColumn::class,
                    'statsAttribute' => 'media',
                    'attribute' => 'as_media',
                ],
                [
                    'attribute' => 'as_er',
                    'format' => ['percent', 2],
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
