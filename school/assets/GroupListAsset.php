<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class GroupListAsset extends AssetBundle
{
    public $sourcePath = '@school/views/groupteacher/assets';

    public $css = [];

    public $js = [
        'js/index.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}