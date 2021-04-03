<?php

namespace school\widgets\filters\models;

use school\widgets\filters\FiltersWidget;

/**
 * Class FilterDropDown
 * @package school\widgets\filters\models
 *
 * @property array $options
 * @property string|null|false $prompt
 */
class FilterDropDown extends BaseFilterItem
{
    public $type = FiltersWidget::FIELD_TYPE_DROPDOWN;
    /** @var array */
    public $options;
    /** @var string|null|false */
    public $prompt;
}