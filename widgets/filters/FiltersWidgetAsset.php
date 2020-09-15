<?php


namespace app\widgets\filters;

use conquer\momentjs\MomentjsAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class FiltersWidgetAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/filters/assets';

    public $css = [];
    public $js = [
        'js/filters.js',
    ];

    public $depends = [
        JqueryAsset::class,
        MomentjsAsset::class,
    ];
}