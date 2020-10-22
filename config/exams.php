<?php

$localPath = __DIR__ . '/local';

$options = [];
if (file_exists("{$localPath}/options.exams.php")) {
    $localOptions = require("{$localPath}/options.exams.php");
    $options = array_merge($options, $localOptions);
}

return [
    'defaultRoute' => 'exams/site/index',
    'modules' => [
        'exams' => [
            'class' => 'app\modules\exams\Exams',
            'layout' => 'main',
        ],
    ],
    'components' => [
        'errorHandler' => [
            'class' => 'yii\web\ErrorHandler',
            'errorAction' => 'exams/site/error',
        ],
        'session' => [
            'name' => 'EXAMS_ID',
        ],
        'request' => [
            'cookieValidationKey' => $options['cookieValidationKey'] ?? '',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'rules' => [
                '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>'          => '<module>/<controller>/<action>',
                '<module:[\w-]+>/<controller:[\w-]+>'                          => '<module>/<controller>/index',
                '<controller:[\w-]+>/<action:[\w-]+>'                          => '/',
            ],
        ],
        'user' => [
            'identityClass' => 'app\modules\exams\models\Auth',
            'loginUrl'      => '/exams/site/login',
        ],
    ],
];