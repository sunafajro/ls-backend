<?php

namespace school\widgets\filters\models;

use school\widgets\filters\FiltersWidget;

/**
 * Class FilterDateAdditionalButtons
 * @package school\widgets\filters\models
 *
 * @property string $dateStartClass
 * @property string $dateEndClass
 * @property string $format
 */
class FilterDateAdditionalButtons extends BaseFilterItem
{
    public $type = FiltersWidget::DATE_ADDITIONAL_BUTTONS;
    /** @var string */
    public $dateStartClass;
    /** @var string */
    public $dateEndClass;
    /** @var string */
    public $format = 'yyyy-mm-dd';
}