<?php

namespace app\modules\school\assets;

use app\assets\SuggestionsAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class AddressAutocompleteAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/teacher/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
        SuggestionsAsset::class,
    ];
}