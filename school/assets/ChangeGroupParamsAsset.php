<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ChangeGroupParamsAsset extends AssetBundle
{
    public $sourcePath = '@school/views/groupteacher/assets';

    public $css = [];

    public $js = [
        'js/change-group-params.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
