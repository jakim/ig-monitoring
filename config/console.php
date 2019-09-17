<?php

use pahanini\log\ConsoleTarget;
use yii\faker\FixtureController;
use yii\log\FileTarget;

date_default_timezone_set('UTC');

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'app\commands',
    'container' => require __DIR__ . '/container.php',
    'components' => [
        'queue' => require __DIR__ . '/queue.php',
        'cache' => require __DIR__ . '/cache.php',
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'fileMode' => 0777,
                    'dirMode' => 0777,
                ],
                [
                    'class' => FileTarget::class,
                    'levels' => ['profile'],
                    'categories' => ['yii\db\*'],
                    'dirMode' => 0777,
                    'fileMode' => 0777,
                    'logFile' => '@runtime/logs/profile.log',
                    'logVars' => [],
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => [
                        'app\components\instagram\*',
                    ],
                    'logFile' => '@runtime/logs/instagram.log',
                    'logVars' => [],
                    'dirMode' => 0777,
                    'fileMode' => 0777,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => [
                        'app\components\services\*',
                    ],
                    'logFile' => '@runtime/logs/services.log',
                    'logVars' => [],
                    'dirMode' => 0777,
                    'fileMode' => 0777,
                ],
                [
                    'class' => ConsoleTarget::class,
                    'levels' => ['error', 'warning', 'info', 'trace'],
                    'categories' => [
                        'app\components\instagram*',
                        'app\components\services*',
                        'app\components\http*',
                    ],
                    'logVars' => [],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
//            'migrationPath' => null,
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
            ],
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
