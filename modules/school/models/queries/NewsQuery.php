<?php

namespace app\modules\school\models\queries;

use app\modules\school\models\News;
use yii\db\ActiveQuery;

/**
 * Class NewsQuery
 * @package app\models\queries
 *
 * @method News one($db = null)
 * @method News[] all($db = null)
 */

class NewsQuery extends ActiveQuery
{
    /**
     * @param int $id
     * @return NewsQuery|ActiveQuery
     */
    public function byId(int $id) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $id]);
    }

    /**
     * @param int[] $ids
     * @return NewsQuery|ActiveQuery
     */
    public function byIds(array $ids) : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.id" => $ids]);
    }

    /**
     * @return NewsQuery|ActiveQuery
     */
    public function active() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 1]);
    }

    /**
     * @return NewsQuery|ActiveQuery
     */
    public function deleted() : ActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.visible" => 0]);
    }
}