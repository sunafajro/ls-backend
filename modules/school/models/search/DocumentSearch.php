<?php

namespace app\modules\school\models\search;

use app\modules\school\models\Document;

/**
 * Class DocumentSearch
 * @package app\modules\school\models\search
 */
class DocumentSearch extends FileSearch
{
    const ENTITY_CLASS = Document::class;
}