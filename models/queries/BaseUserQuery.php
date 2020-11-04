<?php

namespace app\models\queries;

use app\models\BaseUser;
use yii\db\ActiveQuery;

/**
 * Class BaseUserQuery
 * @package app\models\queries
 *
 * @method BaseUser one($db = null)
 * @method BaseUser[] all($db = null)
 */

class BaseUserQuery extends ActiveQuery
{
    /**
     * @param int $id
     * @return BaseUserQuery|ActiveQuery
     */
    public function byId(int $id) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param int[] $ids
     * @return BaseUserQuery|ActiveQuery
     */
    public function byIds(array $ids) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(['in', "{$tableName}.id", $ids]);
    }

    /**
     * @param int[] $ids
     * @return BaseUserQuery|ActiveQuery
     */
    public function byExceptIds($ids)
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(['not in', "{$tableName}.id", $ids]);
    }

    /**
     * @param string $login
     * @return BaseUserQuery|ActiveQuery
     */
    public function byLogin(string $login) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.login" => $login]);
    }

    /**
     * @return BaseUserQuery|ActiveQuery
     */
    public function active() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 1]);
    }

    /**
     * @return BaseUserQuery|ActiveQuery
     */
    public function deleted() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 0]);
    }
}