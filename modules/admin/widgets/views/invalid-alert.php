<?php

use yii\helpers\Html;

/* @var $ico string */
/* @var $lines array */
/* @var $this \yii\web\View */

?>

<div class="alert alert-<?= $alert ?>">
    <span class="fa fa-<?= $icon ?>"></span> <strong><?= Html::encode($header) ?></strong>
    <p>
        <?php foreach ($lines as $line): ?>
            <?= Html::encode($line) ?><br>
        <?php endforeach; ?>
    </p>
</div>
