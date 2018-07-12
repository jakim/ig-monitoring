<?php

use app\modules\admin\components\grid\StatsColumn;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var app\modules\admin\models\TagSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $dailyDiff
 * @var array $monthlyDiff
 */

$this->title = 'Monitoring :: Tags';
$this->params['breadcrumbs'][] = 'Monitoring';
$this->params['breadcrumbs'][] = 'Tags';
?>
<div class="tag-index nav-tabs-custom">

    <?= $this->render('_tabs') ?>

    <div class="tab-content table-responsive">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => \yii\grid\SerialColumn::class],

                [
                    'attribute' => 'name',
                    'content' => function (\app\models\Tag $model) {
                        return Html::a($model->namePrefixed, ['tag/stats', 'id' => $model->id]);
                    },
                ],
                [
                    'class' => StatsColumn::class,
                    'statsAttribute' => 'media',
                    'attribute' => 'ts_media',
                    'dailyDiff' => $dailyDiff,
                    'monthlyDiff' => $monthlyDiff,
                ],
                [
                    'class' => StatsColumn::class,
                    'statsAttribute' => 'likes',
                    'attribute' => 'ts_likes',
                    'dailyDiff' => $dailyDiff,
                    'monthlyDiff' => $monthlyDiff,
                ],
                [
                    'class' => StatsColumn::class,
                    'statsAttribute' => 'comments',
                    'attribute' => 'ts_comments',
                    'dailyDiff' => $dailyDiff,
                    'monthlyDiff' => $monthlyDiff,
                ],
                [
                    'class' => StatsColumn::class,
                    'statsAttribute' => 'min_likes',
                    'attribute' => 'ts_min_likes',
                    'dailyDiff' => $dailyDiff,
                    'monthlyDiff' => $monthlyDiff,
                ],
                [
                    'class' => StatsColumn::class,
                    'statsAttribute' => 'max_likes',
                    'attribute' => 'ts_max_likes',
                    'dailyDiff' => $dailyDiff,
                    'monthlyDiff' => $monthlyDiff,
                ],
                [
                    'class' => StatsColumn::class,
                    'statsAttribute' => 'min_comments',
                    'attribute' => 'ts_min_comments',
                    'dailyDiff' => $dailyDiff,
                    'monthlyDiff' => $monthlyDiff,
                ],
                [
                    'class' => StatsColumn::class,
                    'statsAttribute' => 'max_comments',
                    'attribute' => 'ts_max_comments',
                    'dailyDiff' => $dailyDiff,
                    'monthlyDiff' => $monthlyDiff,
                ],
                [
                    'attribute' => 'ts_created_at',
                    'label' => 'Updated At',
                    'format' => 'date',
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                ],
            ],
        ]); ?>

        <?= \app\modules\admin\widgets\CreateMonitoringModal::widget([
            'trackerType' => \app\dictionaries\TrackerType::TAG,
        ]) ?>

    </div>
</div>
