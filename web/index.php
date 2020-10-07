<?php
use yii\helpers\ArrayHelper;
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', isset($_SERVER['YII_DEBUG']) ? $_SERVER['YII_DEBUG'] : 'true');
defined('YII_ENV') or define('YII_ENV', isset($_SERVER['YII_ENV']) ? $_SERVER['YII_ENV'] : 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$yiiActiveApp = isset($_SERVER['YII_ACTIVE_APP']) && $_SERVER['YII_ACTIVE_APP']
    ? $_SERVER['YII_ACTIVE_APP']
    : 'school';

$config = ArrayHelper::merge(
    require(__DIR__ . "/../config/web.php"),
    require(__DIR__ . "/../config/{$yiiActiveApp}.php")
);

(new yii\web\Application($config))->run();
