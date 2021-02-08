<?php

namespace school\models\queries;

use school\models\Role;

/**
 * Class RoleQuery
 * @package app\modules\school\models\queries
 *
 * @method Role      one($db = null)
 * @method Role[]    all($db = null)
 * @method RoleQuery byId(int $id)
 * @method RoleQuery byIds(array $ids)
 * @method RoleQuery byActive()
 * @method RoleQuery byDeleted()
 * @method RoleQuery byModuleType(string $moduleType)
 */
class RoleQuery extends \common\models\queries\RoleQuery
{

}
