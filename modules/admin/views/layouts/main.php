<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */


app\assets\AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);

/** @var \app\models\User $user */
$user = Yii::$app->user->identity;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!--    <meta name="referrer" content="never">-->
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> :: <?= Html::encode(Yii::$app->name) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper">

    <header class="main-header">

        <?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

        <nav class="navbar navbar-static-top" role="navigation">

            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">

                <ul class="nav navbar-nav">

                    <li class="margin">
                        <?= Html::beginForm(['/admin/auth/logout'], 'post') ?>
                        <?= Html::submitButton('Logout (' . $user->username . ')', ['class' => 'btn btn-default btn-sm']) ?>
                        <?= Html::endForm() ?>
                    </li>

                </ul>
            </div>
        </nav>
    </header>

    <aside class="main-sidebar">

        <section class="sidebar">

            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <?= Html::img($user->image, ['class' => 'img-circle']) ?>
                </div>
                <div class="pull-left info">
                    <p><?= Html::encode($user->username) ?></p>
                    <span class="small text-muted"><?= Html::encode($user->email) ?></span>
                </div>
            </div>

            <?= dmstr\widgets\Menu::widget([
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items' => [
                    ['label' => 'Menu', 'options' => ['class' => 'header']],
                        ['label' => 'Monitoring', 'icon' => 'line-chart', 'url' => ['/admin/monitoring/accounts'], 'active' => $this->context->id == 'monitoring'],
                ],
            ]) ?>

            <?= \app\modules\admin\widgets\FavoritesMenu::widget() ?>

        </section>

    </aside>


    <div class="content-wrapper">
        <?= \app\modules\admin\widgets\ProxyAlert::widget() ?>
        <section class="content-header">
            <h1>
                <?php
                if ($this->title !== null) {
                    echo \yii\helpers\Html::encode($this->title);
                } ?>
            </h1>

            <?= \yii\widgets\Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        </section>

        <section class="content">
            <?= \app\widgets\Alert::widget() ?>
            <?= $content ?>
        </section>
    </div>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
