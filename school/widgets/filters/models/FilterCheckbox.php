<?php

namespace school\widgets\filters\models;

use school\widgets\filters\FiltersWidget;

/**
 * Class FilterCheckbox
 * @package school\widgets\filters\models
 */
class FilterCheckbox extends BaseFilterItem
{
    public $type = FiltersWidget::FIELD_TYPE_CHECKBOX;
}