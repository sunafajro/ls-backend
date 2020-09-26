<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class BookOrderPositionStudentFormAsset extends AssetBundle
{
    public $sourcePath = '@app/views/book-order-position/assets';

    public $css = [];

    public $js = [
        'js/manage-students.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}