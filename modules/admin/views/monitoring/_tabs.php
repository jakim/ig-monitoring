<?= \yii\bootstrap\Nav::widget([
    'items' => [
        ['label' => 'Accounts', 'url' => ['/admin/monitoring/accounts']],
        ['label' => 'Tags', 'url' => ['/admin/monitoring/tags']],
    ],
    'options' => ['class' => 'nav nav-tabs'], // set this to nav-tabs or nav-pills to get tab-styled navigation
]); ?>
