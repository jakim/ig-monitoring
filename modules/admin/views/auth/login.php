<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 26.01.2018
 */

use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;

?>

    <p class="login-box-msg">Sign in to start your session</p>

<?php $authAuthChoice = AuthChoice::begin([
    'popupMode' => false,
    'autoRender' => false,
]) ?>
<?php foreach ($authAuthChoice->getClients() as $client): ?>
    <div class="form-group">
        <?= Html::a('Sign in with ' . $client->getTitle(), ['/admin/auth/auth', 'authclient' => $client->getName(),], ['class' => "btn btn-success btn-block $client->name "]) ?>
    </div>
<?php endforeach; ?>
<?php AuthChoice::end() ?>