<?php

namespace common\models\queries;

use common\models\BasePoll;

/**
 * Class BasePollQuery
 * @package app\models\queries
 *
 * @method BasePoll one($db = null)
 * @method BasePoll[] all($db = null)
 * @method BasePollQuery byId(int $id)
 * @method BasePollQuery byIds(array $ids)
 * @method BasePollQuery active()
 * @method BasePollQuery deleted()
 */

class BasePollQuery extends BaseActiveQuery
{
    /**
     * @return BasePollQuery
     */
    public function inProgress() : BasePollQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.active" => 1]);
    }

    /**
     * @param string $type
     * @return BasePollQuery
     */
    public function byEntityType(string $type) : BasePollQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.entity_type" => $type]);
    }
}