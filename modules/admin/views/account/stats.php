<?php

use yii\grid\GridView;
use yii\grid\SerialColumn;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

$this->title = "{$model->usernamePrefixed} :: Statistics";
$this->params['breadcrumbs'][] = ['label' => 'Monitoring', 'url' => ['monitoring/accounts']];
$this->params['breadcrumbs'][] = ['label' => $model->usernamePrefixed, 'url' => ['dashboard', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Statistics';

$formatter = Yii::$app->formatter;
$lastAccountStats = $model->lastAccountStats;

?>
<div class="account-view">
    <div class="row">
        <div class="col-lg-3">
            <?= $this->render('_profile', ['model' => $model]) ?>
        </div>
        <div class="col-lg-9">
            <div class="nav-tabs-custom">
                <?= $this->render('_tabs', ['model' => $model]) ?>
                <div class="tab-content">
                    <?= $this->render('_tools-header', [
                        'model' => $model,
                        'routes' => [
                            'table' => '/admin/account/stats',
                            'download' => ['/admin/account/stats', 'export' => 1],
                        ],
                    ]) ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => SerialColumn::class],
                            'followed_by:integer',
                            'follows:integer',
                            'media:integer',
                            [
                                'attribute' => 'er',
                                'format' => ['percent', 2],
                            ],
                            [
                                'attribute' => 'avg_likes',
                                'format' => ['decimal', 1],
                            ],
                            [
                                'attribute' => 'avg_comments',
                                'format' => ['decimal', 1],
                            ],
                            'created_at:date',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
