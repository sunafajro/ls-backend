<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class UserViewAsset extends AssetBundle
{
    public $sourcePath = '@school/views/user/assets';

    public $css = [];

    public $js = [
        'js/view.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}