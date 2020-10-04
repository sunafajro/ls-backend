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
                'school/app/references/<tag>'                                  => 'school/references/app-<tag>',
                'school/app/references/list/<name:\w+>'                        => 'school/references/app-list',
                'school/app/references/create/<name:\w+>'                      => 'school/references/app-create',
                'school/app/references/delete/<name:\w+>/<id:\d+>'             => 'school/references/app-delete',
                'school/app/schedule/<tag>'                                    => 'school/schedule/app-<tag>',
                'school/app/user/<tag>'                                        => 'school/user/app-<tag>',

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