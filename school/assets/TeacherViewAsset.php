<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class TeacherViewAsset extends AssetBundle
{
    public $sourcePath = '@school/views/teacher/assets';

    public $css = [];

    public $js = [
        'js/view.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}