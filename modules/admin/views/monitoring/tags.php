<?php

use app\dictionaries\TagInvalidationType;
use app\modules\admin\components\grid\StatsColumn;
use jakim\ig\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\CreateMonitoringModal;
use app\dictionaries\TrackerType;

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
                'emptyText' => sprintf('<p class="lead">This is a screen where you can compare metrics for multiple tags.</p>%s',
                    CreateMonitoringModal::widget([
                        'trackerType' => TrackerType::TAG,
                        'modalToggleButton' => [
                            'label' => 'Add first tag',
                            'class' => 'btn btn-lg btn-success',
                        ],
                    ])),
                'emptyTextOptions' => [
                    'class' => 'text-center empty',
                ],
                'columns' => [
                    ['class' => \yii\grid\SerialColumn::class],

                    [
                        'attribute' => 'name',
                        'content' => function (\app\models\Tag $model) {
                            $html = [];
                            $html[] = Html::a($model->namePrefixed, ['tag/stats', 'id' => $model->id]);
                            $html[] = Html::a('<span class="fa fa-external-link text-sm"></span>', Url::tag($model->name), ['target' => '_blank']);

                            if (!$model->is_valid) {
                                $html[] = sprintf(
                                    '<span class="fa fa-exclamation-triangle text-danger pull-right" data-toggle="tooltip" data-placement="top"  title="%s, attempts: %s"></span>',
                                    TagInvalidationType::getLabel($model->invalidation_type_id, 'Unknown reason'),
                                    $model->invalidation_count
                                );
                            }
                            if ($model->disabled) {
                                $html[] = '<span class="fa fa-exclamation-triangle text-danger pull-right" title="Not found."></span>';
                            }

                            return implode(" \n", $html);
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

            <?php if ($dataProvider->totalCount): ?>
                <?= CreateMonitoringModal::widget([
                    'trackerType' => TrackerType::TAG,
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

<?php
$this->registerJs('jQuery(\'[data-toggle="tooltip"]\').tooltip()');
