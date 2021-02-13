<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-school',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'controllerNamespace' => 'school\controllers',
    'defaultRoute' => '/site/index',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-school',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'school\models\Auth',
            'loginUrl'      => '/site/login',
            'enableAutoLogin' => true,
            'authTimeout'     => 1200,
            'identityCookie' => ['name' => '_identity-school', 'httpOnly' => true],
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
                    'basePath' => '@school/messages',
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
                'schedule/<action:[\w-]+>' => 'schedule/index',
                'app/references/<tag>' => 'references/app-<tag>',
                'app/references/list/<name:\w+>' => 'references/app-list',
                'app/references/create/<name:\w+>' => 'references/app-create',
                'app/references/delete/<name:\w+>/<id:\d+>' => 'references/app-delete',
                'app/schedule/<tag>' => 'schedule/app-<tag>',
                'app/user/<tag>' => 'user/app-<tag>',

                '<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
                '<controller:[\w-]+>' => '<controller>/index',
                //'<controller:[\w-]+>/<action:[\w-]+>' => '/',
            ],
        ],
    ],
    'params' => $params,
    'aliases' => [
        '@uploads' => '@school/web/uploads',
    ],
];
