<?php

namespace school\models\searches;

use school\models\Role;
use school\models\User;

/**
 * Class UserSearch
 * @package school\models\searches
 */
class UserSearch extends \common\models\searches\UserSearch
{
    const ENTITY_CLASS = User::class;
    const ROLE_CLASS = Role::class;
}