<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class GroupViewAsset extends AssetBundle
{
    public $sourcePath = '@school/views/groupteacher/assets';

    public $css = [];

    public $js = [
        'js/view.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}