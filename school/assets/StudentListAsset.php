<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class StudentListAsset extends AssetBundle
{
    public $sourcePath = '@school/views/studname/assets';

    public $css = [];

    public $js = [
        'js/index.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}