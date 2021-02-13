<?php

namespace common\components\helpers;

use yii\helpers\Html;

class IconHelper {
    /**
     * @param string $name
     * 
     * @return string
     */
    public static function icon(string $name) : string
    {
        return Html::tag('i', '', ['class' => "fa fa-{$name}", 'aria-hidden' => 'true']);
    }
}