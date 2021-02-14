<?php

namespace school\assets;

use yii\web\AssetBundle;

class PrintReceiptAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/print_receipt.css'
    ];
    public $js = [];
    public $depends = [];
}
