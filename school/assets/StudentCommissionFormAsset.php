<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class StudentCommissionFormAsset extends AssetBundle
{
    public $sourcePath = '@school/views/student-commission/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}