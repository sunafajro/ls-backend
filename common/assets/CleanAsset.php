<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class CleanAsset
 * @package common\assets
 */
class CleanAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [];
    public $depends = [];
}
