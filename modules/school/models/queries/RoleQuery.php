<?php

namespace app\modules\school\models\queries;

use app\models\queries\BaseRoleQuery;
use app\modules\school\models\Role;
use yii\db\ActiveQuery;

/**
 * Class RoleQuery
 * @package app\modules\school\models\queries
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