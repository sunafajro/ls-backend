<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class AddressAutocompleteAsset extends AssetBundle
{
    public $sourcePath = '@app/views/teacher/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}