<?php

namespace app\modules;

use app\modules\client\Client;
use app\modules\exam\Exam;
use app\modules\school\School;
use Yii;
use yii\base\Module;
use yii\console\Application;

class BaseModule extends Module
{
    // система учета
    const MODULE_SCHOOL  = 'school';
    // кабинет клиента
    const MODULE_CLIENT  = 'client';
    // экзамены
    const MODULE_EXAM    = 'exam';

    const MODULE_CONSOLE = 'console';
    const MODULE_UNKNOWN = 'unknown';

    /**
     * @return string
     */
    public static function getCurrentModuleName()
    {
        $currentModule = Yii::$app->controller->module ?? null;

        if ($currentModule instanceof School) {
            return self::MODULE_SCHOOL;
        } else if ($currentModule instanceof Client) {
            return self::MODULE_CLIENT;
        } else if ($currentModule instanceof Exam) {
            return self::MODULE_EXAM;
        } elseif ($currentModule instanceof  Application) {
            return self::MODULE_CONSOLE;
        }

        return self::MODULE_UNKNOWN;
    }
}