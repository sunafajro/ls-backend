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
        'school' => [
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'rules' => [
                'school/schedule/<action:[\w-]+>'                              => 'school/schedule/index',
                'school/api/schedule/<tag>'                                    => 'school/schedule/api-<tag>',
                'school/api/user/<tag>'                                        => 'school/user/api-<tag>',
                'school/api/references/<tag>'                                  => 'school/references/api-<tag>',
                'school/api/references/list/<name:\w+>'                        => 'school/references/api-list',
                'school/api/references/create/<name:\w+>'                      => 'school/references/api-create',
                'school/api/references/delete/<name:\w+>/<id:\d+>'             => 'school/references/api-delete',

                '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>'          => '<module>/<controller>/<action>',
                '<module:[\w-]+>/<controller:[\w-]+>'                          => '<module>/<controller>/index',
                '<controller:[\w-]+>/<action:[\w-]+>'                          => '/',
            ],
        ],
        'user' => [
            'loginUrl' => '/school/site/login',
        ],
    ],
];