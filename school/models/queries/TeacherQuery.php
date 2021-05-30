<?php

namespace school\models\queries;

use common\models\queries\BaseActiveQuery;
use school\models\Teacher;

/**
 * Class CityQuery
 * @package school\models\queries
 *
 * @method Teacher one($db = null)
 * @method Teacher[] all($db = null)
 * @method TeacherQuery byId(int $id)
 * @method TeacherQuery byIds(array $id)
 * @method TeacherQuery deleted()
 */
class TeacherQuery extends BaseActiveQuery
{
    /**
     * @return TeacherQuery
     */
    public function active(): BaseActiveQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere([
            "{$tableName}.visible" => 1,
            "{$tableName}.old" => 0,
        ]);
    }
}