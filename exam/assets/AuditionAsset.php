<?php

namespace exam\assets;

use yii\web\AssetBundle;

/**
 * Class AuditionAsset
 * @package exam\assets
 */
class AuditionAsset extends AssetBundle
{
    public $sourcePath = '@exam/views/site/assets';

    public $css = [
        'css/vendors.css',
        'css/app.css',
    ];

    public $js = [
        'js/vendors.js',
        'js/app.js',
    ];

    public $depends = [];
}