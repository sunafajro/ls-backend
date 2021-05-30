<?php

namespace common\components\helpers;

/**
 * Class ArrayHelper
 * @package common\components\helpers
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * @param array $options
     * @param string $promptText
     *
     * @return array
     */
    public static function unshiftOption(array $options, string $promptText = '-select-'): array
    {
        $result = [];

        foreach($options as $key => $value) {
            $result[] = ['key' => $key, 'value' => $value];
        }

        array_unshift($result, ['key' => null, 'value' => \Yii::t('app', $promptText)]);

        return ArrayHelper::map($result, 'key', 'value');
    }
}