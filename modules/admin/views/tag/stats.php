<?php

use yii\grid\GridView;
use yii\grid\SerialColumn;

/* @var $this yii\web\View */
/* @var $model app\models\Tag */

$this->title = "{$model->namePrefixed} :: Statistics";
$this->params['breadcrumbs'][] = ['label' => 'Monitoring', 'url' => ['monitoring/tags']];
$this->params['breadcrumbs'][] = ['label' => $model->namePrefixed, 'url' => ['stats', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Statistics';

$formatter = Yii::$app->formatter;
$lastAccountStats = $model->lastTagStats;

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
                            'table' => '/admin/tag/stats',
                            'download' => ['/admin/tag/stats', 'export' => 1],
                        ],
                    ]) ?>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => SerialColumn::class],
                            'media:integer',
                            'likes:integer',
                            'min_likes:integer',
                            'max_likes:integer',
                            'comments:integer',
                            'min_comments:integer',
                            'max_comments:integer',
                            'created_at:date',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
