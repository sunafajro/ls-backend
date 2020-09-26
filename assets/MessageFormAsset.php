<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class MessageFormAsset extends AssetBundle
{
    public $sourcePath = '@app/views/message/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}