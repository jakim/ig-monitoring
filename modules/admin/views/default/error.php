<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<section class="content box box-danger">

    <div class="error-page box-body">
        <h2 class="headline text-info"><i class="fa fa-warning text-red"></i></h2>

        <div class="error-content">
            <h4><?= $name ?></h4>

            <p>
                <?= nl2br(Html::encode($message)) ?>
            </p>

            <p>
                The above error occurred while the Web server was processing your request.
                Please contact us if you think this is a server error. Thank you.
                Meanwhile, you may <a href='<?= Yii::$app->homeUrl ?>'>return to dashboard</a>.
            </p>
        </div>
    </div>

</section>
