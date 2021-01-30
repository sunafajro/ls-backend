<?php

namespace app\modules\school\models;

use app\models\BaseRole;
use app\modules\school\models\queries\RoleQuery;
use app\modules\school\School;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "roles".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $description
 * @property integer $visible
 * @property string  $module_type
 *
 * @method static RoleQuery|ActiveQuery find()
 */
class Role extends BaseRole
{
    const DEFAULT_FIND_CLASS = RoleQuery::class;
    const DEFAULT_MODULE_TYPE = School::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return array_merge([
            [['module_type'], 'default', 'value' => School::MODULE_NAME]
        ], parent::rules());
    }
}
