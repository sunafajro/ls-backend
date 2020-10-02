<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SuggestionsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css',
    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js',
        'https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/js/jquery.suggestions.min.js',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
}