<?php

namespace client\models;

use client\models\queries\GroupFileQuery;
use yii\db\ActiveQuery;

/**
 * Class GroupFile
 * @package client\models
 *
 * @method static GroupFileQuery|ActiveQuery find()
 */
class GroupFile extends File
{
    const DEFAULT_ENTITY_TYPE = self::TYPE_GROUP_FILES;
    const DEFAULT_FIND_CLASS = GroupFileQuery::class;
}