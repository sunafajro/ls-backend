<?php

namespace client\models;

use client\models\queries\MessageFileQuery;
use yii\db\ActiveQuery;

/**
 * Class MessageFile
 * @package client\models
 *
 * @method static MessageFileQuery|ActiveQuery find()
 */
class MessageFile extends File
{
    const DEFAULT_ENTITY_TYPE = self::TYPE_MESSAGE_FILES;
    const DEFAULT_FIND_CLASS = MessageFileQuery::class;
}