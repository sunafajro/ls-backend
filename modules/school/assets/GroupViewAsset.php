<?php

namespace app\modules\school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class GroupViewAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/groupteacher/assets';

    public $css = [];

    public $js = [
        'js/view.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}