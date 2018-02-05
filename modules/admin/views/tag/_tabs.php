<?php

use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

?>

<?= Nav::widget([
    'items' => [
        ['label' => 'Statistics', 'url' => ['/admin/tag/stats', 'id' => $model->id]],
//        ['label' => 'Media Tags', 'url' => ['/admin/account/media-tags', 'id' => $model->id]],
//        ['label' => 'Linked Accounts (TODO)', 'url' => ['#']],
    ],
    'options' => ['class' => 'nav nav-tabs'], // set this to nav-tabs or nav-pills to get tab-styled navigation
]); ?>
