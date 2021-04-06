<?php

namespace exam\assets;

use common\assets\FontawesomeAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        FontawesomeAsset::class,
    ];
}
