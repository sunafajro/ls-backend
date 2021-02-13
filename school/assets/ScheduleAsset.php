<?php

namespace school\assets;

use yii\web\AssetBundle;

class ScheduleAsset extends AssetBundle
{
    public $sourcePath = '@school/views/schedule/assets';

    public $css = [];
    
    public $js = [
        'js/chunk-vendors.js',
        'js/app.js',
    ];

    public $depends = [
        NotyAsset::class,
    ];
}