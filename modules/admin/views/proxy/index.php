<?php

use app\components\ArrayHelper;
use app\models\Proxy;
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

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'ip',
                    'format' => 'html',
                    'value' => function(Proxy $model) {
                        return Html::a($model->ip, ['proxy/update', 'id' => $model->id]);
                    },
                ],
                'port',
                [
                    'attribute' => 'tagString',
                    'value' => function(Proxy $model) {
                        return implode(', ', ArrayHelper::getColumn($model->tags, 'name'));
                    },

                ],
                'created_at:date',

                [
                    'class' => \yii\grid\ActionColumn::class,
                    'template' => '{update} {delete}',
                ],
            ],
        ]); ?>

        <p>
            <?= Html::a('Create', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
        </p>
    </div>
</div>
