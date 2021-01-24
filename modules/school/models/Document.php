<?php

namespace app\modules\school\models;

use app\modules\school\models\queries\DocumentQuery;
use yii\db\ActiveQuery;

/**
 * Class Document
 * @package app\modules\school\models
 *
 * @method static DocumentQuery|ActiveQuery find()
 */
class Document extends File
{
    const DEFAULT_ENTITY_TYPE = self::TYPE_DOCUMENTS;
    const DEFAULT_FIND_CLASS = DocumentQuery::class;
}