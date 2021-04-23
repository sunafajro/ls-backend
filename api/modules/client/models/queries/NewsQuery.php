<?php

namespace api\modules\client\models\queries;

/**
 * Class NewsQuery
 * @package api\modules\client\models\queries
 *
 * @method NewsQuery active()
 */
class NewsQuery extends \common\models\queries\BaseActiveQuery
{
    /**
     * @return NewsQuery
     */
    public function sent(): NewsQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.send" => 1]);
    }
}