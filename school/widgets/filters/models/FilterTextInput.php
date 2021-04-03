<?php

namespace school\widgets\filters\models;

use school\widgets\filters\FiltersWidget;

/**
 * Class FilterTextInput
 * @package school\widgets\filters\models
 */
class FilterTextInput extends BaseFilterItem
{
    public $type = FiltersWidget::FIELD_TYPE_TEXT_INPUT;
}