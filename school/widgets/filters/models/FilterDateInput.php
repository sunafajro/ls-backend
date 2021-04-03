<?php

namespace school\widgets\filters\models;

use school\widgets\filters\FiltersWidget;

/**
 * Class FilterDateInput
 * @package school\widgets\filters\models
 *
 * @property string $format
 * @property array $addClasses
 */
class FilterDateInput extends BaseFilterItem
{
    public $type = FiltersWidget::FIELD_TYPE_DATE_INPUT;
    /** @var string */
    public $format = 'yyyy-mm-dd';
    /** @var array */
    public $addClasses = [];
}