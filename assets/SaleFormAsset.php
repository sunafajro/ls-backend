<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SaleFormAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/sale/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}