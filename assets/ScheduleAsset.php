<?php

namespace app\assets;

use yii\web\AssetBundle;

class ScheduleAsset extends AssetBundle
{
    public $sourcePath = '@app/views/schedule/assets';

    public $css = [];
    
    public $js = [
        'js/chunk-vendors.js',
        'js/app.js',
    ];

    public $depends = [];
}