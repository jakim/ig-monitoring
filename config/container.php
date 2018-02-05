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
    ],
];