<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SaleFormAsset extends AssetBundle
{
    public $sourcePath = '@school/views/sale/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}