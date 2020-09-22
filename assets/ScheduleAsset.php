<?php

namespace app\assets;

use yii\web\AssetBundle;

class ScheduleAsset extends AssetBundle
{
    public $basePath = '@webroot';
    
    public $baseUrl = '@web';
    
    public $css = [];
    
    public $js = [
        'js/schedule/chunk-vendors.js',
        'js/schedule/app.js',
    ];

    public $depends = [];
}