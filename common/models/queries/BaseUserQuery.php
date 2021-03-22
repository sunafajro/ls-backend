<?php

namespace common\models\queries;

use common\models\BaseUser;

/**
 * Class BaseUserQuery
 * @package app\models\queries
 *
 * @method BaseUser one($db = null)
 * @method BaseUser[] all($db = null)
 * @method BaseUserQuery byId(int $id)
 * @method BaseUserQuery byIds(array $ids)
 * @method BaseUserQuery active()
 * @method BaseUserQuery deleted()
 */

class BaseUserQuery extends BaseActiveQuery
{
    /**
     * @param int[] $ids
     * @return BaseUserQuery
     */
    public function byExceptIds(array $ids): BaseUserQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(['not in', "{$tableName}.id", $ids]);
    }

    /**
     * @param string $login
     * @return BaseUserQuery
     */
    public function byLogin(string $login): BaseUserQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.login" => $login]);
    }
}