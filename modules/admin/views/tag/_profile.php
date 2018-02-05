<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tag */

$formatter = Yii::$app->formatter;
/** @var \app\models\TagStats $lastTagStats */
$lastTagStats = $model->lastTagStats;

?>
<div class="box box-primary">

    <div class="box-body box-profile">
        <h3 class="profile-username text-center">
            <?= Html::encode($model->namePrefixed) ?>
        </h3>
        <p class="text-muted text-center">
            from top 9 posts
        </p>
        <?php if ($lastTagStats): ?>
            <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                    <b><?= $lastTagStats->getAttributeLabel('media') ?></b>
                    <a class="pull-right">
                        <?= $formatter->asInteger($lastTagStats->media) ?>
                    </a>
                </li>
                <li class="list-group-item">
                    <b><?= $lastTagStats->getAttributeLabel('likes') ?></b>
                    <a class="pull-right">
                        <?= $formatter->asInteger($lastTagStats->likes) ?>
                    </a>
                    <br>
                    <small>min</small>
                    <small class="pull-right">
                        <?= $formatter->asInteger($lastTagStats->min_likes) ?>
                    </small>
                    <br>
                    <small>max</small>
                    <small class="pull-right">
                        <?= $formatter->asInteger($lastTagStats->max_likes) ?>
                    </small>
                </li>
                <li class="list-group-item">
                    <b><?= $lastTagStats->getAttributeLabel('comments') ?></b>
                    <a class="pull-right">
                        <?= $formatter->asInteger($lastTagStats->comments) ?>
                    </a>
                    <br>
                    <small>min</small>
                    <small class="pull-right">
                        <?= $formatter->asInteger($lastTagStats->min_comments) ?>
                    </small>
                    <br>
                    <small>max</small>
                    <small class="pull-right">
                        <?= $formatter->asInteger($lastTagStats->max_comments) ?>
                    </small>
                </li>
                <li class="list-group-item">
                    <b><?= $lastTagStats->getAttributeLabel('created_at') ?></b>
                    <a class="pull-right">
                        <?= $formatter->asDatetime($lastTagStats->created_at) ?>
                    </a>
                </li>
            </ul>
        <?php endif; ?>
        <?= Html::a($model->monitoring ? '<span class="fa fa-stop"></span> Turn off monitoring' : '<span class="fa fa-play"></span> Turn on monitoring', ['monitoring', 'id' => $model->id], [
            'class' => 'btn btn-block ' . ($model->monitoring ? 'btn-danger' : 'btn-success'),
            'data' => [
                'method' => 'post',
                'confirm' => 'Are you sure?',
            ],
        ]) ?>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Description</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
<!--        <strong><i class="fa fa-external-link margin-r-5"></i>-->
<!--            Title-->
<!--        </strong>-->
<!--        <p class="text-muted">-->
<!--            description-->
<!--        </p>-->
<!--        <hr>-->
        <strong>
            <i class="fa fa-tags margin-r-5"></i> Tags (TODO)
            <a href="#" class="btn btn-xs btn-link">add</a>
        </strong>

        <p>
            <span class="label label-danger">UI Design</span>
            <span class="label label-success">Coding</span>
            <span class="label label-info">Javascript</span>
            <span class="label label-warning">PHP</span>
            <span class="label label-primary">Node.js</span>
        </p>

        <hr>

        <strong>
            <i class="fa fa-file-text-o margin-r-5"></i> Notes (TODO)
            <a href="#" class="btn btn-xs btn-link">add</a>
        </strong>

        <p>
            Administrator notes
        </p>
    </div>
    <!-- /.box-body -->
</div>