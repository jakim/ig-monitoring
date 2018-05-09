<?php

return [
    'class' => \yii\queue\db\Queue::class,
    'attempts' => 5,
    'ttr' => 15,
    'mutex' => \yii\mutex\MysqlMutex::class,
];