<?php

namespace app\models\queries;

use app\models\EducationLevel;
use yii\db\ActiveQuery;

/**
 * Class EducationLevelQuery
 * @package app\models\queries
 *
 * @method EducationLevel one($db = null)
 * @method EducationLevel[] all($db = null)
 */

class EducationLevelQuery extends ActiveQuery
{
    /**
     * @param int $id
     * @return EducationLevelQuery|ActiveQuery
     */
    public function byId(int $id) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param int[] $ids
     * @return EducationLevelQuery|ActiveQuery
     */
    public function byIds(array $ids) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $ids]);
    }

    /**
     * @return EducationLevelQuery|ActiveQuery
     */
    public function active() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 1]);
    }

    /**
     * @return EducationLevelQuery|ActiveQuery
     */
    public function deleted() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 0]);
    }
}