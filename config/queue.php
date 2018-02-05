<?php

return [
    'class' => \yii\queue\db\Queue::class,
    'mutex' => \yii\mutex\MysqlMutex::class,
];