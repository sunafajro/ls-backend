<?php

namespace school\models\queries;

use common\models\queries\BaseActiveQuery;
use school\models\UserTimeTracking;

/**
 * Class NewsQuery
 * @package school\models\queries
 *
 * @method UserTimeTracking one($db = null)
 * @method UserTimeTracking[] all($db = null)
 * @method UserTimeTrackingQuery byId(int $id)
 * @method UserTimeTrackingQuery byIds(array $id)
 * @method UserTimeTrackingQuery active()
 * @method UserTimeTrackingQuery deleted()
 */

class UserTimeTrackingQuery extends BaseActiveQuery
{
    /**
     * @param int $id
     * @return UserTimeTrackingQuery
     */
    public function byEntityId(int $id): UserTimeTrackingQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.entity_id" => $id]);
    }
}