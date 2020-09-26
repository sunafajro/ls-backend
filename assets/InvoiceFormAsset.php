<?php

namespace app\assets;

use yii\web\AssetBundle;

class InvoiceFormAsset extends AssetBundle
{
    public $sourcePath = '@app/views/invoice/assets';

    public $css = [];

    public $js = [
        'js/bundle.js',
    ];

    public $depends = [];
}