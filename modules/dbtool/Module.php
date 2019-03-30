<?php

namespace app\modules\dbtool;

use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\dbtool\commands';
 
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
     
    /**
     * Определяем параметры загрузки модуля
     * 
     * @param \yii\console\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\dbtool\commands';
        }
    }
}