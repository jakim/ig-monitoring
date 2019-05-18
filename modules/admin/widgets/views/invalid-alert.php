<?php

use app\modules\admin\widgets\AjaxButton;
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
    <?php if (isset($updateUrl)): ?>
        <p>
            <?= AjaxButton::widget([
                'url' => $updateUrl,
                'text' => 'force update',
                'options' => [
                    'class' => 'btn btn-link btn-xs',
                    'style' => 'padding-left: 0',
                    'data' => [
                        'style' => 'zoom-out',
                    ],
                ],
            ]) ?>
        </p>
    <?php endif; ?>
</div>
