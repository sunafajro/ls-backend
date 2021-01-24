<?php

namespace app\modules\school\models;

use app\modules\school\models\queries\GroupFileQuery;
use yii\db\ActiveQuery;

/**
 * Class GroupFile
 * @package app\modules\school\models
 *
 * @method static GroupFileQuery|ActiveQuery find()
 */
class GroupFile extends File
{
    const DEFAULT_ENTITY_TYPE = self::TYPE_GROUP_FILES;
    const DEFAULT_FIND_CLASS = GroupFileQuery::class;
}