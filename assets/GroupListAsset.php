<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class GroupListAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/groupteacher/assets';

    public $css = [];

    public $js = [
        'js/index.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}