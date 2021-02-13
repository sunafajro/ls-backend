<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class StudentViewAsset extends AssetBundle
{
    public $sourcePath = '@school/views/studname/assets';

    public $css = [];

    public $js = [
        'js/view.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}