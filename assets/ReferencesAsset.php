<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ReferencesAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/references/assets';

    public $css = [];

    public $js = [
        'js/vendors.js',
        'js/app.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}