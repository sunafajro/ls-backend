<?php

namespace exam\assets;

use yii\web\AssetBundle;

/**
 * Class SpeakingExamAsset
 * @package exam\assets
 */
class SpeakingExamAsset extends AssetBundle
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