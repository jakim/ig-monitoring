<?php

use app\components\ArrayHelper;
use app\models\Account;
use app\modules\admin\models\MonitoringForm;
use app\modules\admin\widgets\OnOffMonitoringButton;
use jakim\ig\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

$this->title = "{$model->usernamePrefixed} :: Media Accounts";
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
                        <p>
                            <?= \yii\helpers\Html::a('CSV Export', \yii\helpers\Url::current(['export' => 1])) ?>
                        </p>
                        <?= \yii\grid\GridView::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => [
                                ['class' => \yii\grid\SerialColumn::class],
                                [
                                    'attribute' => 'username',
                                    'format' => 'raw',
                                    'value' => function (Account $model, $key, $index, $column) {
                                        if ($model->monitoring) {
                                            $value = Html::a($model->usernamePrefixed, ['account/dashboard', 'id' => $model->id]);
                                        } else {
                                            $value = $model->{$column->attribute};
                                        }

                                        return $value . ' ' . Html::a('<span class="fa fa-external-link text-sm"></span>', Url::account($model->username), ['target' => '_blank']);
                                    },
                                ],
                                [
                                    'attribute' => 'er',
                                    'format' => 'raw',
                                    'value' => function (Account $model) use ($formatter) {
                                        if ($model->er) {
                                            return sprintf('<span data-toggle="tooltip" data-placement="top" title="updated at: %s">%s</span>',
                                                $formatter->asDate($model->stats_updated_at),
                                                $formatter->asPercent($model->er)
                                            );
                                        }

                                        return '';
                                    },
                                ],
                                'occurs',
                                [
                                    'format' => 'raw',
                                    'value' => function (Account $account) use ($model) {
                                        return OnOffMonitoringButton::widget([
                                            'model' => $account,
                                            'form' => new MonitoringForm([
//                                                'scenario' => 'account',
                                                'names' => $account->username,
                                                'categories' => ArrayHelper::getColumn($model->tags, 'name'),
                                                'proxy_id' => $model->proxy_id,
                                            ]),
                                            'btnCssClass' => 'btn btn-xs',
                                            'offAjaxOptions' => [
                                                'success' => new \yii\web\JsExpression('function(){location.reload();}'),
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
