<?php

namespace app\modules\exams\assets;

use yii\web\AssetBundle;

class AuditionAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/exams/views/site/assets';

    public $css = [
        'css/vendors.css',
        'css/app.css',
    ];

    public $js = [
        'js/vendors.js',
        'js/app.js',
    ];

    public $depends = [];
}