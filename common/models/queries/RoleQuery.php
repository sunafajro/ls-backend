<?php

namespace common\models\queries;

use common\models\Role;

/**
 * Class RoleQuery
 * @package app\models\queries
 *
 * @method Role one($db = null)
 * @method Role[] all($db = null)
 * @method RoleQuery byId(int $id)
 * @method RoleQuery byIds(array $ids)
 * @method RoleQuery byActive()
 * @method RoleQuery byDeleted()
 */
class RoleQuery extends ActiveQuery
{
    /**
     * @param string $moduleType
     * @return RoleQuery
     */
    public function byModuleType(string $moduleType): RoleQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.module_type" => $moduleType]);
    }
}