<?php

namespace school\assets;

use yii\web\AssetBundle;

class NotyAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.min.css',
    ];
    public $js = [];
    public $depends = [];
}