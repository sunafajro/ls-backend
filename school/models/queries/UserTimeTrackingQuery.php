<?php

namespace school\models\queries;

use school\models\UserTimeTracking;
use yii\db\ActiveQuery;

/**
 * Class NewsQuery
 * @package school\models\queries
 *
 * @method UserTimeTracking one($db = null)
 * @method UserTimeTracking[] all($db = null)
 */

class UserTimeTrackingQuery extends ActiveQuery
{
    /**
     * @param int $id
     * @return UserTimeTrackingQuery|ActiveQuery
     */
    public function byId(int $id) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param int[] $ids
     * @return UserTimeTrackingQuery|ActiveQuery
     */
    public function byIds(array $ids) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $ids]);
    }

    /**
     * @param int $id
     * @return UserTimeTrackingQuery|ActiveQuery
     */
    public function byEntityId(int $id) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.entity_id" => $id]);
    }

    /**
     * @return UserTimeTrackingQuery|ActiveQuery
     */
    public function active() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 1]);
    }

    /**
     * @return UserTimeTrackingQuery|ActiveQuery
     */
    public function deleted() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 0]);
    }
}