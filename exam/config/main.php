<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-exam',
    'name' => 'Система тестирования',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
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
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@exam/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error'=>'error.php'
                    ],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'GET site/get-exam-file/<name:\w{13}>'         => 'site/get-exam-file',
                '<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>'          => '<controller>/<action>',
                '<controller:[\w-]+>'                          => '<controller>/index',
            ],
        ],
    ],
    'params' => $params,
    'aliases' => [
        '@examData' => '@data/files/exam',
    ],
];
