<?php

namespace common\components\helpers;

use yii\helpers\Html;

/**
 * Class IconHelper
 * @package common\components\helpers
 */
class IconHelper {
    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $title
     * @param string|null $version
     *
     * @return string
     */
    public static function icon(string $name, string $label = null, string $title = null, string $version = null) : string
    {
        $className = $version === 'fa5' ? "fas fa-{$name}" : "fa fa-{$name}";
        return Html::tag(
            'i',
            '',
            [
                'class' => $className,
                'aria-hidden' => 'true',
                'title' => $title,
            ]
        ) . ($label ? " {$label}" : '');
    }
}