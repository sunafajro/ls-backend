<?php
    namespace app\assets;
    use Yii;
    use yii\web\AssetBundle;

    if (Yii::$app->params['appMode'] === 'bitrix') {
        class AppAsset extends AssetBundle
        {
            public $basePath = '@webroot';
            public $baseUrl = '@web';
            public $css = [
                'css/offcanvas_bitrix.css',
                'fa/css/font-awesome.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.min.css',
                'https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css',
                'css/site_bitrix.css',
            ];
            public $js = [
                'js/offcanvas.js',
                'https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js',
                'https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/js/jquery.suggestions.min.js',
                '/js/menu/vendors.js',
                '/js/menu/bundle.js'
            ];
            public $depends = [
                'yii\web\YiiAsset',
                'yii\bootstrap\BootstrapAsset',
                'yii\bootstrap\BootstrapPluginAsset',
            ];
        }
    } else {
        class AppAsset extends AssetBundle
        {
            public $basePath = '@webroot';
            public $baseUrl = '@web';
            public $css = [
                'css/offcanvas_standalone.css',
                'fa/css/font-awesome.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.min.css',
                'https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css',
                'css/site_standalone.css',
            ];
            public $js = [
                'js/offcanvas.js',
                'https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js',
                'https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/js/jquery.suggestions.min.js'
            ];
            public $depends = [
                'yii\web\YiiAsset',
                'yii\bootstrap\BootstrapAsset',
                'yii\bootstrap\BootstrapPluginAsset',
            ];
        }
    }
