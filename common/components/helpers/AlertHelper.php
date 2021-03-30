<?php

namespace common\components\helpers;

use yii\helpers\Html;

/**
 * Class AlertHelper
 * @package common\components\helpers
 */
class AlertHelper
{
    public static function alert(string $message, string $color = 'danger', string $version = null) : string
    {
        return Html::tag('div', $message, ['class' => "alert alert-{$color}"]);
    }
}