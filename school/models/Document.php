<?php

namespace school\models;

use school\models\queries\DocumentQuery;
use yii\db\ActiveQuery;

/**
 * Class Document
 * @package school\models
 *
 * @method static DocumentQuery|ActiveQuery find()
 */
class Document extends File
{
    const DEFAULT_ENTITY_TYPE = self::TYPE_DOCUMENTS;
    const DEFAULT_FIND_CLASS = DocumentQuery::class;
}