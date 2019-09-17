<?php

use app\components\ArrayHelper;
use app\models\Account;
use app\modules\admin\components\grid\AccountBasicStatColumn;
use app\modules\admin\components\grid\AccountColumn;
use app\modules\admin\models\MonitoringForm;
use app\modules\admin\widgets\OnOffMonitoringButton;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Account */
/* @var $categories app\models\Category[] */

$this->title = "{$model->usernamePrefixed} :: Mentioned Accounts";
$this->params['breadcrumbs'][] = ['label' => 'Monitoring', 'url' => ['monitoring/accounts']];
$this->params['breadcrumbs'][] = ['label' => $model->usernamePrefixed, 'url' => ['dashboard', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Media Accounts';

$formatter = Yii::$app->formatter;
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
                                'table' => '/admin/account/media-accounts',
                                'download' => ['/admin/account/media-accounts', 'export' => 1],
                            ],
                        ]) ?>

                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => [
                                ['class' => SerialColumn::class],
                                [
                                    'class' => AccountColumn::class,
                                    'attribute' => 'username',
                                ],
                                [
                                    'class' => AccountBasicStatColumn::class,
                                    'attribute' => 'er',
                                    'dataFormat' => ['percent', 2],
                                ],
                                [
                                    'class' => AccountBasicStatColumn::class,
                                    'attribute' => 'followed_by',
                                    'dataFormat' => 'integer',
                                ],
                                'occurs',
                                [
                                    'format' => 'raw',
                                    'value' => function (Account $account) use ($model, $categories) {
                                        return OnOffMonitoringButton::widget([
                                            'model' => $account,
                                            'form' => new MonitoringForm([
                                                'names' => $account->username,
                                                'categories' => ArrayHelper::getColumn($categories, 'name'),
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
                <div class="alert alert-info">
                    <span class="fa fa-info-circle"></span> Obtained from media captions.
                </div>
            </div>
        </div>
    </div>

<?php
$this->registerJs('jQuery(\'[data-toggle="tooltip"]\').tooltip()');
