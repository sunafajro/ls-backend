<?php

namespace app\modules\school\models;

use app\models\BaseRole;
use app\modules\school\School;

/**
 * This is the model class for table "roles".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $description
 * @property integer $visible
 * @property string  $module_type
 */
class Role extends BaseRole
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge([
            [['module_type'], 'default', 'value' => School::MODULE_NAME]
        ], parent::rules());
    }

    /**
     * Метод возвращает список доступных ролей пользователей в виде многомерного массива.
     *
     * @return array
     */
    public static function getRolesList() : array
    {
        return self::find()
            ->select([
                'id'          => 'id',
                'name'        => 'name',
                'description' => 'description'
            ])
            ->from(self::tableName())
            ->where([
                'visible' => 1,
                'module_type' => School::MODULE_NAME,
            ])
            ->orderby(['id' => SORT_ASC])
            ->asArray()
            ->all();
    }
}
