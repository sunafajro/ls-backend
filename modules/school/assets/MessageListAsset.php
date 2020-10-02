<?php

namespace app\modules\school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class MessageListAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/message/assets';

    public $css = [];

    public $js = [
        'js/index.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}