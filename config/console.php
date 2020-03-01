<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'modules' => [],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'db' => $db,
    ],
    'params' => $params,
    'aliases' => [
        '@attestates' => '@app/data/attestates',
        '@uploads'    => '@app/web/uploads',
    ],
];

if (file_exists(__DIR__ . '/local/console.php')) {
    $config = array_merge($config, require(__DIR__ . '/local/console.php'));
}

return $config;