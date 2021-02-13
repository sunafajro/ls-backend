<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class JournalGroupFormAsset extends AssetBundle
{
    public $sourcePath = '@school/views/journalgroup/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}