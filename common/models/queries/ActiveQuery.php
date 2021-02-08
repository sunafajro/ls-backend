<?php

namespace common\models\queries;

use common\models\ActiveRecord;

/**
 * Class ActiveQuery
 * @package app\models\queries
 *
 * @method ActiveRecord one($db = null)
 * @method ActiveRecord[] all($db = null)
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @param int $id
     * @return ActiveQuery
     */
    public function byId(int $id): ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param int[] $ids
     * @return ActiveQuery
     */
    public function byIds(array $ids): ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $ids]);
    }

    /**
     * @return ActiveQuery
     */
    public function byActive(): ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 1]);
    }

    /**
     * @return ActiveQuery
     */
    public function byDeleted(): ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 0]);
    }
}