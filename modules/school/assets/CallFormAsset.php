<?php

namespace app\modules\school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class CallFormAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/call/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}