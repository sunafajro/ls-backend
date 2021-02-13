<?php

namespace school\models\searches;

use common\models\searches\BaseFileSearch;
use school\models\File;

/**
 * Class FileSearch
 * @package school\models\searches
 */
class FileSearch extends BaseFileSearch
{
    const ENTITY_CLASS = File::class;
}