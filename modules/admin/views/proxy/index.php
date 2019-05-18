<?php

use app\models\Proxy;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\ProxySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Proxies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proxy-index box">

    <div class="box-body">

        <div class="row">

            <div class="col-lg-12 text-right">
                <?= Html::a('Add proxy', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
            </div>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'ip',
                    'format' => 'html',
                    'value' => function (Proxy $model) {
                        return Html::a($model->ip, ['proxy/update', 'id' => $model->id]);
                    },
                ],
                'port',
                'created_at:date',

                [
                    'class' => ActionColumn::class,
                    'template' => '{update} {delete}',
                ],
            ],
        ]); ?>
    </div>
</div>
