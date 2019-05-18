<?php

use app\models\Tag;
use app\modules\admin\models\MonitoringForm;
use app\modules\admin\widgets\OnOffMonitoringButton;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

$this->title = "{$model->usernamePrefixed} :: Media Tags";
$this->params['breadcrumbs'][] = ['label' => 'Monitoring', 'url' => ['monitoring/accounts']];
$this->params['breadcrumbs'][] = ['label' => $model->usernamePrefixed, 'url' => ['dashboard', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Media Tags';

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
                            'table' => '/admin/account/media-tags',
                            'download' => ['/admin/account/media-tags', 'export' => 1],
                        ],
                    ]) ?>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => SerialColumn::class],
                            'name',
                            'occurs',
                            [
                                'attribute' => 'ts_avg_likes',
                                'label' => 'Avg Likes',
                                'format' => ['decimal', 2],
                            ],
                            [
                                'format' => 'raw',
                                'value' => function (Tag $tag) use ($model) {
                                    return OnOffMonitoringButton::widget([
                                        'model' => $tag,
                                        'form' => new MonitoringForm([
                                            'names' => $tag->name,
                                            'proxy_id' => $model->proxy_id,
                                        ]),
                                        'btnCssClass' => 'btn btn-xs',
                                        'offAjaxOptions' => [
                                            'success' => new JsExpression('function(){location.reload();}'),
                                        ],
                                    ]);
                                },
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
