<?php

namespace school\widgets\filters;

use yii\base\Widget;

/**
 * Class FiltersWidget
 * @package school\widgets\filters
 */
class FiltersWidget extends Widget
{
    const FIELD_TYPE_TEXT_INPUT   = 'textInput';
    const FIELD_TYPE_DROPDOWN     = 'dropdown';
    const FIELD_TYPE_DATE_INPUT   = 'dateInput';
    const ADDITIONAL_DATE_BUTTONS = 'dateButtons';

    /** @var array  */
    public $actionUrl = [];
    /** @var array */
    public $items = [];

    /**
     * @return string[]
     */
    protected static function getFilterTypes(): array
    {
        return [
            self::FIELD_TYPE_TEXT_INPUT   => '_input',
            self::FIELD_TYPE_DROPDOWN     => '_select',
            self::FIELD_TYPE_DATE_INPUT   => '_date',
            self::ADDITIONAL_DATE_BUTTONS => '_dateButtons',
        ];
    }

    /**
     * @return string
     */
    public function run() {
        return $this->render('filters', [
            'actionUrl'   => $this->actionUrl,
            'filterTypes' => self::getFilterTypes(),
            'items'       => $this->items,
        ]);
    }
}