<?php

namespace school\models\queries;

use school\models\UserImage;
use yii\db\ActiveQuery;

/**
 * Class UserImageQuery
 * @package school\models\queries
 *
 * @method UserImage one($db = null)
 * @method UserImage[] all($db = null)
 * @method UserImageQuery|ActiveQuery byId(int $id)
 * @method UserImageQuery|ActiveQuery byEntityId(int $id)
 * @method UserImageQuery|ActiveQuery byUserId(int $id)
 */
class UserImageQuery extends FileQuery
{

}