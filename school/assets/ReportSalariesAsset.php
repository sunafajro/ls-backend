<?php

namespace school\assets;

use yii\web\AssetBundle;

class ReportSalariesAsset extends AssetBundle
{
    public $sourcePath = '@school/views/report/assets';

    public $css = [];

    public $js = [
        'js/chunk-vendors.js',
        'js/app.js',
    ];

    public $depends = [
        NotyAsset::class,
    ];
}