<?php

namespace app\widgets\autocomplete;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class AutoCompleteWidgetAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/autocomplete/assets';

    public $css = [
        'css/autocomplete.css',
    ];
    public $js = [
        'js/autocomplete.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
