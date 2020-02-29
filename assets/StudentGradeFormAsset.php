<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class StudentGradeFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    
    public $baseUrl = '@web';
    
    public $css = [];
    
    public $js = [
        'js/students/attestationForm.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}