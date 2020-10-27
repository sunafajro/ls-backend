<?php

namespace app\modules\school\assets;

use app\assets\NotyAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ReportAccrualsAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/report/assets';

    public $css = [];

    public $js = [
        'js/accruals.js',
    ];

    public $depends = [
        JqueryAsset::class,
        NotyAsset::class,
    ];
}