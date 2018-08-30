<?php

use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

?>

<?= Nav::widget([
    'items' => [
        ['label' => 'Statistics', 'url' => ['/admin/tag/stats', 'id' => $model->id]],
        ['label' => '<span class="fa fa-gears"></span>', 'url' => ['/admin/tag/settings', 'id' => $model->id],
            'options' => ['class' => 'pull-right'],
            'linkOptions' => ['class' => 'text-muted'],
            'encode' => false],
    ],
    'options' => ['class' => 'nav nav-tabs'], // set this to nav-tabs or nav-pills to get tab-styled navigation
]); ?>
