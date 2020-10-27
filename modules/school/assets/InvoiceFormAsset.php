<?php

namespace app\modules\school\assets;

use yii\web\AssetBundle;

class InvoiceFormAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/invoice/assets';

    public $css = [];

    public $js = [
        'js/bundle.js',
    ];

    public $depends = [];
}