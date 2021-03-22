<?php

namespace common\models\queries;

use common\models\BaseFile;

/**
 * Class BaseFileQuery
 * @package app\models\queries
 *
 * @method BaseFile one($db = null)
 * @method BaseFile[] all($db = null)
 * @method BaseFileQuery byId(int $id)
 * @method BaseFileQuery byIds(array $id)
 */
class BaseFileQuery extends BaseActiveQuery
{
    /**
     * @param int $entityId
     * @return BaseFileQuery
     */
    public function byEntityId(int $entityId): BaseFileQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.entity_id" => $entityId]);
    }

    /**
     * @param string $entityType
     * @return BaseFileQuery
     */
    public function byEntityType(string $entityType): BaseFileQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.entity_type" => $entityType]);
    }

    /**
     * @param int $userId
     * @return BaseFileQuery
     */
    public function byUserId(int $userId): BaseFileQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.user_id" => $userId]);
    }
}