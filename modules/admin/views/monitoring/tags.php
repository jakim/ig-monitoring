<?php

use app\modules\admin\components\grid\TagStatsColumn;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\TagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
                    'content' => function(\app\models\Tag $model) {
                        return Html::a($model->namePrefixed, ['tag/stats', 'id' => $model->id]);
                    },
                ],
                [
                    'class' => TagStatsColumn::class,
                    'statsAttribute' => 'media',
                    'attribute' => 'ts_media',
                ],
                [
                    'class' => TagStatsColumn::class,
                    'statsAttribute' => 'likes',
                    'attribute' => 'ts_likes',
                ],
                [
                    'class' => TagStatsColumn::class,
                    'statsAttribute' => 'comments',
                    'attribute' => 'ts_comments',
                ],
                [
                    'class' => TagStatsColumn::class,
                    'statsAttribute' => 'min_likes',
                    'attribute' => 'ts_min_likes',
                ],
                [
                    'class' => TagStatsColumn::class,
                    'statsAttribute' => 'max_likes',
                    'attribute' => 'ts_max_likes',
                ],
                [
                    'class' => TagStatsColumn::class,
                    'statsAttribute' => 'min_comments',
                    'attribute' => 'ts_min_comments',
                ],
                [
                    'class' => TagStatsColumn::class,
                    'statsAttribute' => 'max_comments',
                    'attribute' => 'ts_max_comments',
                ],
                'created_at:date',
            ],
        ]); ?>
    </div>
</div>
