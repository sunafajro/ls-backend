<?php

namespace app\modules\school\assets;

use app\assets\NotyAsset;
use yii\web\AssetBundle;

class ReportSalariesAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/report/assets';

    public $css = [];

    public $js = [
        'js/chunk-vendors.js',
        'js/app.js',
    ];

    public $depends = [
        NotyAsset::class,
    ];
}