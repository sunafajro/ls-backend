<?php

namespace common\models\queries;

use yii\db\ActiveQuery;

/**
 * Class BaseActiveQuery
 * @package common\models\queries
 */
class BaseActiveQuery extends ActiveQuery
{
    /**
     * @param int $id
     * @return BaseActiveQuery
     */
    public function byId(int $id) : BaseActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param int[] $ids
     * @return BaseActiveQuery
     */
    public function byIds(array $ids) : BaseActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $ids]);
    }

    /**
     * @return BaseActiveQuery
     */
    public function active() : BaseActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 1]);
    }

    /**
     * @return BaseActiveQuery
     */
    public function deleted() : BaseActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 0]);
    }
}