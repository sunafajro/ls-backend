<?php

namespace school\widgets\filters\models;

use yii\base\Model;

/**
 * Class BaseFilterItem
 * @package school\widgets\filters\models
 *
 * @property string $type
 * @property string $title
 * @property string $name
 * @property mixed $value
 */
class BaseFilterItem extends Model
{
    /** @var string */
    public $type;
    /** @var string */
    public $title;
    /** @var string */
    public $name;
    /** @var mixed */
    public $value;
}