<?php

namespace school\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class AddressAutocompleteAsset
 * @package school\assets
 */
class AddressAutocompleteAsset extends AssetBundle
{
    public $sourcePath = '@school/views/teacher/assets';

    public $css = [];

    public $js = [
        'js/form.js'
    ];

    public $depends = [
        JqueryAsset::class,
        SuggestionsAsset::class,
    ];
}