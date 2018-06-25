<?php

use app\modules\admin\widgets\ChangeInfoBox;
use yii\helpers\ArrayHelper;
use app\models\AccountStats;
use yii\helpers\Html;

/**
 * @var array $change
 * @var string $header
 */

$model = new AccountStats();

?>

<h2 class="page-header">
    <?= Html::encode($header) ?>
<!--    <sup><span class="fa fa-question-circle-o text-info"></span></sup>-->
</h2>
<div class="row">
    <div class="col-lg-3">
        <?= ChangeInfoBox::widget([
            'header' => $model->getAttributeLabel('er'),
            'number' => ArrayHelper::getValue($change, 'er'),
            'format' => ['percent', 2],
        ]) ?>
    </div>
    <div class="col-lg-3">
        <?= ChangeInfoBox::widget([
            'header' => $model->getAttributeLabel('followed_by'),
            'number' => ArrayHelper::getValue($change, 'followed_by'),
        ]) ?>
    </div>
    <div class="col-lg-3">
        <?= ChangeInfoBox::widget([
            'header' => $model->getAttributeLabel('follows'),
            'number' => ArrayHelper::getValue($change, 'follows'),
        ]) ?>
    </div>
    <div class="col-lg-3">
        <?= ChangeInfoBox::widget([
            'header' => $model->getAttributeLabel('media'),
            'number' => ArrayHelper::getValue($change, 'media'),
        ]) ?>
    </div>
</div>