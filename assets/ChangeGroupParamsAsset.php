<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ChangeGroupParamsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/change-group-params.js',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
}
