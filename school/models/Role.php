<?php

namespace school\models;

use school\models\queries\RoleQuery;
use school\School;
use yii\db\ActiveQuery;

/**
 * Class Role
 * @package school\models
 *
 * @property integer $id
 * @property string  $name
 * @property string  $description
 * @property integer $visible
 * @property string  $module_type
 *
 * @method static RoleQuery|ActiveQuery find()
 */
class Role extends \common\models\Role
{
    const DEFAULT_FIND_CLASS = RoleQuery::class;
    const DEFAULT_MODULE_TYPE = School::MODULE_SLUG;
}