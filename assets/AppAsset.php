<?php
namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/offcanvas.css',
        'fa/css/font-awesome.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.min.css',
        'https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css',
        'css/site.css',
    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js',
        'https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/js/jquery.suggestions.min.js',
        'js/offcanvas.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
