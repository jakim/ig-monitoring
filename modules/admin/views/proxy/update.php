<?php

/* @var $this yii\web\View */
/* @var $model app\models\Proxy */

$this->title = "Update Proxy: {$model->ip}";
$this->params['breadcrumbs'][] = ['label' => 'Proxies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ip, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="proxy-update box">
    <div class="box-body">

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>
