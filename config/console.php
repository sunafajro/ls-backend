<?php

$localPath = __DIR__ . '/local';

$db  = require(__DIR__ . '/db.php');
if (file_exists("{$localPath}/db.php")) {
    $localDb = require("{$localPath}/db.php");
    $db = array_merge($db, $localDb);
}

$params = require(__DIR__ . '/params.php');
if (file_exists("{$localPath}/params.php")) {
    $localParams = require("{$localPath}/params.php");
    $params = array_merge($params, $localParams);
}

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

return $config;