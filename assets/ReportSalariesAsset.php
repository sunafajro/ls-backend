<?php

namespace app\assets;

use yii\web\AssetBundle;

class ReportSalariesAsset extends AssetBundle
{
    public $sourcePath = '@app/views/report/assets';

    public $css = [];

    public $js = [
        'js/vendors.js',
        'js/app.js',
    ];

    public $depends = [];
}