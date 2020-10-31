<?php

namespace app\modules\exams\models;

use app\models\BaseRole;
use app\modules\exams\Exams;

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
                'module_type' => Exams::MODULE_NAME,
            ])
            ->orderby(['id' => SORT_ASC])
            ->asArray()
            ->all();
    }
}
