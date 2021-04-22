<?php

namespace api\modules\client;

/**
 * Class Module
 * @package api\modules\client
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'api\modules\client\controllers';

    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }
}