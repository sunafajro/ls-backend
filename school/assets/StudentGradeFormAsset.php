<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class StudentGradeFormAsset extends AssetBundle
{
    public $sourcePath = '@school/views/student-grade/assets';
    
    public $css = [];
    
    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}