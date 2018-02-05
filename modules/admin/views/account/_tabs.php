<?php

use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

?>

<?= Nav::widget([
    'items' => [
        ['label' => 'Statistics', 'url' => ['/admin/account/stats', 'id' => $model->id]],
        ['label' => 'Media Tags', 'url' => ['/admin/account/media-tags', 'id' => $model->id]],
        ['label' => 'Media Accounts', 'url' => ['/admin/account/media-accounts', 'id' => $model->id]],
    ],
    'options' => ['class' => 'nav nav-tabs'], // set this to nav-tabs or nav-pills to get tab-styled navigation
]); ?>
