<?php

namespace client\models\queries;

use client\models\File;
use yii\db\ActiveQuery;

/**
 * Class FileQuery
 * @package client\models\queries
 *
 * @method File one($db = null)
 * @method File[] all($db = null)
 */
class FileQuery extends ActiveQuery
{
    /**
     * @param int $id
     * @return FileQuery|ActiveQuery
     */
    public function byId(int $id) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param int $entityId
     * @return FileQuery|ActiveQuery
     */
    public function byEntityId(int $entityId)
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.entity_id" => $entityId]);
    }

    /**
     * @param int[] $entityIds
     * @return FileQuery|ActiveQuery
     */
    public function byEntityIds(array $entityIds)
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.entity_id" => $entityIds]);
    }

    /**
     * @param int $userId
     * @return FileQuery|ActiveQuery
     */
    public function byUserId(int $userId)
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.user_id" => $userId]);
    }
}