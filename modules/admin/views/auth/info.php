<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 26.01.2018
 */

use yii\helpers\Html; ?>

<div class="alert alert-warning">
    If user account exist, it's not active.
</div>
<p class="text-right">
    <?= Html::a('Log in', ['auth/login']) ?>
</p>
