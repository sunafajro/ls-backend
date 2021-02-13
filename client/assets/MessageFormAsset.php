<?php

namespace client\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class MessageFormAsset
 * @package client\assets
 */
class MessageFormAsset extends AssetBundle
{
    public $sourcePath = '@client/views/student/assets';

    public $css = [];

    public $js = [
        'js/messageForm.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}