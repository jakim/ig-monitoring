<?php

use jakim\ig\Url;
use yii\helpers\Html;

/**
 * @var $model \app\models\Account
 */


/** @var \app\components\Formatter $formatter */
$formatter = Yii::$app->formatter;
?>

<?php if ($model->profile_pic_url): ?>
    <?= Html::img($model->profile_pic_url, ['class' => 'profile-user-img img-responsive img-circle']) ?>
<?php endif; ?>
    <h3 class="profile-username text-center">
        <?= Html::encode($model->usernamePrefixed) ?>
        <?php if ($model->is_verified): ?>
            <i class="fa fa-check-circle text-sm text-blue" title="Verified"></i>
        <?php endif; ?>
        <?= Html::a('<span class="fa fa-external-link text-sm"></span>', Url::account($model->username), ['target' => '_blank']) ?>
    </h3>
    <p class="text-muted text-center">
        <?= Html::encode($model->full_name) ?>
    </p>
<?php if ($model->is_business): ?>
    <p class="text-muted text-center text-sm">
        <span class="fa fa-briefcase"></span> <?= Html::encode($model->business_category) ?><br>
    </p>
<?php endif; ?>
