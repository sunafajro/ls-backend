<?php

namespace app\widgets\autocomplete;

use yii\base\widget;

class AutoCompleteWidget extends Widget {
    public $hiddenField;
    public $searchField;

    public function run() {
        AutoCompleteWidgetAsset::register($this->view);
        return $this->render('autocomplete', [
            'hiddenField' => $this->hiddenField,
            'searchField' => $this->searchField,
        ]);
    }
}