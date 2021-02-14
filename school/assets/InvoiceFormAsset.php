<?php

namespace school\assets;

use yii\web\AssetBundle;

class InvoiceFormAsset extends AssetBundle
{
    public $sourcePath = '@school/views/invoice/assets';

    public $css = [];

    public $js = [
        'js/bundle.js',
    ];

    public $depends = [];
}