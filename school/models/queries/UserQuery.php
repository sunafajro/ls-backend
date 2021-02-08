<?php

namespace school\models\queries;

use school\models\User;

/**
 * Class UserQuery
 * @package common\models\users
 *
 * @method User one($db = null)
 * @method User[] all($db = null)
 *
 * @method UserQuery byId(int $id)
 * @method UserQuery byIds(array $ids)
 * @method UserQuery byActive()
 * @method UserQuery byDeleted()
 * @method UserQuery byModuleType(string $moduleType)
 */
class UserQuery extends \common\models\queries\UserQuery
{

}