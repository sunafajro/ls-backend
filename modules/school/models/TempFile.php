<?php

namespace app\modules\school\models;

use app\modules\school\models\queries\TempFileQuery;
use yii\db\ActiveQuery;

/**
 * Class TempFile
 * @package app\modules\school\models
 *
 * @method static TempFileQuery|ActiveQuery find()
 */
class TempFile extends File
{
    const DEFAULT_ENTITY_TYPE = self::TYPE_TEMP;
    const DEFAULT_FIND_CLASS = TempFileQuery::class;
}