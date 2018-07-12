<?php


/* @var $this yii\web\View */
/* @var $model app\models\Proxy */

$this->title = 'Create Proxy';
$this->params['breadcrumbs'][] = ['label' => 'Proxies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proxy-create box">
    <div class="box-body">

        <?= $this->render('_form', [
            'model' => $model,
            'selectData' => $selectData,

        ]) ?>

    </div>
</div>
