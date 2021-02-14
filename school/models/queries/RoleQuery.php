<?php

namespace school\models\queries;

use common\models\queries\BaseRoleQuery;
use school\models\Role;
use yii\db\ActiveQuery;

/**
 * Class RoleQuery
 * @package school\models\queries
 *
 * @method Role one($db = null)
 * @method Role[] all($db = null)
 * @method RoleQuery|ActiveQuery byId(int $id)
 * @method RoleQuery|ActiveQuery byIds(array $id)
 * @method RoleQuery|ActiveQuery active()
 * @method RoleQuery|ActiveQuery deleted()
 */
class RoleQuery extends BaseRoleQuery
{

}