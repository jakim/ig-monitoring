<?php

use app\modules\admin\widgets\InvalidTagAlert;
use app\modules\admin\widgets\OnOffMonitoringButton;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tag */

$formatter = Yii::$app->formatter;

?>

<?php if (!$model->is_valid): ?>
    <?= InvalidTagAlert::widget([
        'model' => $model,
    ]) ?>
<?php endif; ?>

<div class="box box-primary">

    <div class="box-body box-profile">
        <h3 class="profile-username text-center">
            <?= Html::encode($model->namePrefixed) ?>
        </h3>
        <p class="text-muted text-center">
            from top 9 posts
        </p>
        <?php if ($model->media !== null): ?>
            <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                    <b><?= $model->getAttributeLabel('media') ?></b>
                    <a class="pull-right">
                        <?= $formatter->asInteger($model->media) ?>
                    </a>
                </li>
                <li class="list-group-item">
                    <b><?= $model->getAttributeLabel('likes') ?></b>
                    <a class="pull-right">
                        <?= $formatter->asInteger($model->likes) ?>
                    </a>
                    <br>
                    <small>min</small>
                    <small class="pull-right">
                        <?= $formatter->asInteger($model->min_likes) ?>
                    </small>
                    <br>
                    <small>max</small>
                    <small class="pull-right">
                        <?= $formatter->asInteger($model->max_likes) ?>
                    </small>
                </li>
                <li class="list-group-item">
                    <b><?= $model->getAttributeLabel('comments') ?></b>
                    <a class="pull-right">
                        <?= $formatter->asInteger($model->comments) ?>
                    </a>
                    <br>
                    <small>min</small>
                    <small class="pull-right">
                        <?= $formatter->asInteger($model->min_comments) ?>
                    </small>
                    <br>
                    <small>max</small>
                    <small class="pull-right">
                        <?= $formatter->asInteger($model->max_comments) ?>
                    </small>
                </li>
                <li class="list-group-item">
                    <b><?= $model->getAttributeLabel('stats_updated_at') ?></b>
                    <a class="pull-right">
                        <?= $formatter->asDate($model->stats_updated_at) ?>
                    </a>
                </li>
            </ul>
        <?php endif; ?>
        <?= OnOffMonitoringButton::widget([
            'model' => $model,
        ]) ?>
    </div>
</div>