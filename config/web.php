<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'components' => [
        'request' => [
            'enableCsrfValidation' => false,
            'cookieValidationKey' => 'secret random string goes here',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
	],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'authTimeout' => 1200,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
		    'file' => [
                	'class' => 'yii\log\FileTarget',
                	'levels' => ['error', 'warning','profile'],
		    ],
                ],
            ],
        'db' => require(__DIR__ . '/db.php'),
    	'i18n' => [
        	    'translations' => [
            		'app*' => [
            		    'class' => 'yii\i18n\PhpMessageSource',
                    	    'fileMap' => ['app' => 'app.php','app/error'=>'error.php'],
            		],
        	    ],
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => [
                '/schedule' => '/schedule/index',
                '/schedule/<action>' => '/schedule/index',
                '/api/schedule/<tag>' => '/schedule/api-<tag>',
                '/api/user/<tag>' => '/user/api-<tag>',
            ]
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug']['class'] = 'yii\debug\Module';
    $config['modules']['debug']['allowedIPs'] = ['127.0.0.1','::1', '*'];

    // $config['bootstrap'][] = 'gii';
    // $config['modules']['gii']['class'] = 'yii\gii\Module';
    // $config['modules']['gii']['allowedIPs'] = ['127.0.0.1','::1'];
}

return $config;
