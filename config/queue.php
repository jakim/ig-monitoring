<?php

return [
    'class' => \yii\queue\db\Queue::class,
    'attempts' => 5,
    'mutex' => \yii\mutex\MysqlMutex::class,
];