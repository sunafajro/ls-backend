<?php

namespace app\modules\school\models\search;

use app\models\search\BaseFileSearch;
use app\modules\school\models\File;

/**
 * Class FileSearch
 * @package app\modules\school\models\search
 */
class FileSearch extends BaseFileSearch
{
    const ENTITY_CLASS = File::class;
}