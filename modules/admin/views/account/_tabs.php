<?php

use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

?>

<?= Nav::widget([
    'items' => [
        ['label' => 'Dashboard', 'url' => ['/admin/account/dashboard', 'id' => $model->id]],
        ['label' => 'Statistics', 'url' => ['/admin/account/stats', 'id' => $model->id]],
        ['label' => 'Used Tags', 'url' => ['/admin/account/media-tags', 'id' => $model->id]],
        ['label' => 'Mentioned Accounts', 'url' => ['/admin/account/media-accounts', 'id' => $model->id]],
        ['label' => '<span class="fa fa-gears"></span>', 'url' => ['/admin/account/settings', 'id' => $model->id],
            'options' => ['class' => 'pull-right'],
            'linkOptions' => ['class' => 'text-muted'],
            'encode' => false],
    ],
    'options' => ['class' => 'nav nav-tabs'], // set this to nav-tabs or nav-pills to get tab-styled navigation
]); ?>
