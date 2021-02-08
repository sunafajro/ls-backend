<?php

namespace school\models\queries;

use common\models\queries\ActiveQuery;
use school\models\UserTimeTracking;

/**
 * Class NewsQuery
 * @package app\models\queries
 *
 * @method UserTimeTracking one($db = null)
 * @method UserTimeTracking[] all($db = null)
 * @method UserTimeTrackingQuery byId(int $id)
 * @method UserTimeTrackingQuery byIds(array $ids)
 * @method UserTimeTrackingQuery byActive()
 * @method UserTimeTrackingQuery byDeleted()
 */

class UserTimeTrackingQuery extends ActiveQuery
{
    /**
     * @param int $id
     * @return UserTimeTrackingQuery|ActiveQuery
     */
    public function byEntityId(int $id) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.entity_id" => $id]);
    }
}