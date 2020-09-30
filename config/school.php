<?php

$localPath = __DIR__ . '/local';

$options = [];
if (file_exists("{$localPath}/options.school.php")) {
    $localOptions = require("{$localPath}/options.school.php");
    $options = array_merge($options, $localOptions);
}

return [
    'defaultRoute' => 'school/site/index',
    'modules' => [
        'company' => [
            'class' => 'app\modules\school\School',
            'layout' => 'main',
        ],
    ],
    'components' => [
        'errorHandler' => [
            'class' => 'yii\web\ErrorHandler',
            'errorAction' => 'school/site/error',
        ],
        'session' => [
            'name' => 'SCHOOL_ID',
        ],
        'request' => [
            'cookieValidationKey' => $options['cookieValidationKey'],
        ],
        'user' => [
            'loginUrl' => '/school/site/login',
        ],
    ],
];