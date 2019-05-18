<?php

use yii\mutex\MysqlMutex;
use yii\queue\db\Queue;

return [
    'class' => Queue::class,
    'attempts' => 5,
    'mutex' => MysqlMutex::class,
];