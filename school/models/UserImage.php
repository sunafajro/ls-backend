<?php

namespace school\models;

use school\models\queries\UserImageQuery;
use yii\db\ActiveQuery;

/**
 * Class UserImage
 * @package school\models
 *
 * @method static UserImageQuery|ActiveQuery find()
 */
class UserImage extends File
{
    const DEFAULT_ENTITY_TYPE = self::TYPE_USER_IMAGE;
    const DEFAULT_FIND_CLASS = UserImageQuery::class;

    const MIME_TYPES = ['image/gif', 'image/jpg', 'image/jpeg', 'image/png'];
}