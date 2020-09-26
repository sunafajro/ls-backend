<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class BookOrderPositionFormAsset extends AssetBundle
{
    public $sourcePath = '@app/views/book-order-position/assets';

    public $css = [];

    public $js = [
        'js/form.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}