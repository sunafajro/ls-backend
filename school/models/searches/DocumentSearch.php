<?php

namespace school\models\searches;

use school\models\Document;

/**
 * Class DocumentSearch
 * @package school\models\searches
 */
class DocumentSearch extends FileSearch
{
    const ENTITY_CLASS = Document::class;
}