<?php

return [
    'definitions' => [
        \yii\behaviors\TimestampBehavior::class => [
            'value' => function () {
                return (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
            },
        ],
        \yii\behaviors\SluggableBehavior::class => [
            'immutable' => true,
            'ensureUnique' => true,
            'attribute' => 'name',
        ],
        \kartik\select2\Select2::class => [
            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
        ],
        \yii\data\Pagination::class => [
            'defaultPageSize' => 50,
            'pageSizeLimit' => [1, 100],
        ],
    ],
];