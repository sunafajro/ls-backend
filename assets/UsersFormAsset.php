<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class UsersFormAsset extends AssetBundle
{
    public $sourcePath = '@app/views/user/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}