<?php

namespace common\models\queries;

use common\models\BaseRole;
use yii\db\ActiveQuery;

/**
 * Class BaseRoleQuery
 * @package app\models\queries
 *
 * @method BaseRole one($db = null)
 * @method BaseRole[] all($db = null)
 */

class BaseRoleQuery extends ActiveQuery
{
    /**
     * @param int $id
     * @return BaseRoleQuery|ActiveQuery
     */
    public function byId(int $id) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param int[] $ids
     * @return BaseRoleQuery|ActiveQuery
     */
    public function byIds(array $ids) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $ids]);
    }

    /**
     * @return BaseRoleQuery|ActiveQuery
     */
    public function active() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 1]);
    }

    /**
     * @return BaseRoleQuery|ActiveQuery
     */
    public function deleted() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 0]);
    }
}