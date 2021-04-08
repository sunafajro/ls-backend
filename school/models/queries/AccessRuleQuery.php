<?php

namespace school\models\queries;

use common\models\queries\BaseActiveQuery;
use school\models\AccessRule;

/**
 * Class CityQuery
 * @package school\models\queries
 *
 * @method AccessRule one($db = null)
 * @method AccessRule[] all($db = null)
 * @method AccessRuleQuery byId(int $id)
 * @method AccessRuleQuery byIds(array $id)
 * @method AccessRuleQuery active()
 * @method AccessRuleQuery deleted()
 */
class AccessRuleQuery extends BaseActiveQuery
{
    /**
     * @param string $slug
     * @return AccessRuleQuery
     */
    public function bySlug(string $slug): AccessRuleQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.slug" => $slug]);
    }
}