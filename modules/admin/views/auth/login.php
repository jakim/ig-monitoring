<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 26.01.2018
 */
?>

<p class="login-box-msg">Sign in to start your session</p>

<?= yii\authclient\widgets\AuthChoice::widget([
    'baseAuthUrl' => ['/admin/auth/auth'],
    'popupMode' => false,
]) ?>
