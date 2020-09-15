<?php

namespace app\widgets\autocomplete;

use yii\base\Widget;

class AutoCompleteWidget extends Widget {
    /** @var array */
    public $hiddenField;
    /** @var array */
    public $searchField;

    public function run() {
        AutoCompleteWidgetAsset::register($this->view);
        return $this->render('autocomplete', [
            'hiddenField' => $this->hiddenField,
            'searchField' => $this->searchField,
        ]);
    }
}