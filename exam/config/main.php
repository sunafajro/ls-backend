<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-exam',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'exam\controllers',
    'defaultRoute' => 'site/index',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-exam',
        ],
        'user' => [
            'identityClass' => 'exam\models\Auth',
            'loginUrl'      => '/site/login',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-exam', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'EXAM_ID',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'class' => 'yii\web\ErrorHandler',
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>'          => '<controller>/<action>',
                '<controller:[\w-]+>'                          => '<controller>/index',
                //'<controller:[\w-]+>/<action:[\w-]+>'                          => '/',
            ],
        ],
    ],
    'params' => $params,
];
