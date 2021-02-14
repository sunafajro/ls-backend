<?php

namespace client\assets;

use yii\web\AssetBundle;

/**
 * Class FontAwesomeAsset
 * @package client\assets
 */
class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/components/font-awesome';
    public $css = [
        'css/font-awesome.min.css',
    ];
}