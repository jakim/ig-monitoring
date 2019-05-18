<?php

use kartik\select2\Select2;
use yii\behaviors\TimestampBehavior;
use yii\data\Pagination;

return [
    'definitions' => [
        TimestampBehavior::class => [
            'value' => function () {
                return (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
            },
        ],
        Select2::class => [
            'theme' => Select2::THEME_DEFAULT,
        ],
        Pagination::class => [
            'defaultPageSize' => 50,
            'pageSizeLimit' => [1, 1000],
        ],
    ],
];