<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-client',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'controllerNamespace' => 'client\controllers',
    'defaultRoute' => '/site/index',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-school',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'client\models\Auth',
            'loginUrl'      => '/site/login',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-client', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'SCHOOL_ID',
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
                    'basePath' => '@client/messages',
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
                '<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
    'aliases' => [
        '@uploads' => '@client/web/uploads',
    ],
];
