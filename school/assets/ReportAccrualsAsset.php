<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ReportAccrualsAsset extends AssetBundle
{
    public $sourcePath = '@school/views/report/assets';

    public $css = [];

    public $js = [
        'js/accruals.js',
    ];

    public $depends = [
        JqueryAsset::class,
        NotyAsset::class,
    ];
}