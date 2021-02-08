<?php

namespace common\models\queries;

use common\models\User;

/**
 * Class UserQuery
 * @package common\models\queries
 *
 * @method User one($db = null)
 * @method User[] all($db = null)
 * @method UserQuery byId(int $id)
 * @method UserQuery byIds(array $ids)
 * @method UserQuery byActive()
 * @method UserQuery byDeleted()
 */
class UserQuery extends ActiveQuery
{
    /**
     * @param int[] $ids
     * @return UserQuery
     */
    public function byExceptIds(array $ids): UserQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(['not in', "{$tableName}.id", $ids]);
    }

    /**
     * @param string $username
     * @return UserQuery
     */
    public function byUsername(string $username): UserQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.username" => $username]);
    }

    /**
     * @param string $moduleType
     * @return UserQuery
     */
    public function byModuleType(string $moduleType): UserQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.module_type" => $moduleType]);
    }
}