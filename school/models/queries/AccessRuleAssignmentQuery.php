<?php

namespace school\models\queries;

use common\models\queries\BaseActiveQuery;
use school\models\AccessRuleAssignment;

/**
 * Class AccessRuleAssignmentQuery
 * @package school\models\queries
 *
 * @method AccessRuleAssignment one($db = null)
 * @method AccessRuleAssignment[] all($db = null)
 * @method AccessRuleAssignmentQuery byId(int $id)
 * @method AccessRuleAssignmentQuery byIds(array $id)
 */
class AccessRuleAssignmentQuery extends BaseActiveQuery
{
    /**
     * @param string $slug
     * @return AccessRuleAssignmentQuery
     */
    public function byAccessRuleSlug(string $slug): AccessRuleAssignmentQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.access_rule_slug" => $slug]);
    }

    /**
     * @param integer $id
     * @return AccessRuleAssignmentQuery
     */
    public function byRoleId(int $id): AccessRuleAssignmentQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.role_id" => $id]);
    }

    /**
     * @param integer $id
     * @return AccessRuleAssignmentQuery
     */
    public function byUserId(int $id): AccessRuleAssignmentQuery
    {
        $tableName = $this->getPrimaryTableName();
        return $this->andWhere(["{$tableName}.user_id" => $id]);
    }
}