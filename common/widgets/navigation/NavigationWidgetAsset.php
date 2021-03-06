<?php

namespace common\widgets\navigation;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class NavigationWidgetAsset extends AssetBundle
{
    public $sourcePath = '@common/widgets/navigation/assets';

    public $css = [
        'css/navigation.css',
    ];
    public $js = [
        'js/navigation.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
