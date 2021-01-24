<?php

namespace app\models\queries;

use app\models\BaseFile;
use yii\db\ActiveQuery;

/**
 * Class BaseFileQuery
 * @package app\models\queries
 *
 * @method BaseFile one($db = null)
 * @method BaseFile[] all($db = null)
 */
class BaseFileQuery extends ActiveQuery
{
    /**
     * @param int $id
     * @return BaseFileQuery|ActiveQuery
     */
    public function byId(int $id) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param int $entityId
     * @return BaseFileQuery|ActiveQuery
     */
    public function byEntityId(int $entityId)
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.entity_id" => $entityId]);
    }

    /**
     * @param int $userId
     * @return BaseFileQuery|ActiveQuery
     */
    public function byUserId(int $userId)
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.user_id" => $userId]);
    }
}