<?php

namespace exam\models;

use common\models\BaseRole;
use common\models\queries\BaseRoleQuery;
use exam\Exam;
use yii\db\ActiveQuery;

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
     * @return BaseRoleQuery | ActiveQuery
     */
    public static function find() : ActiveQuery
    {
        $query = parent::find();
        return $query->andWhere(['module_type' => Exam::MODULE_NAME]);
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
                'module_type' => Exam::MODULE_NAME,
            ])
            ->orderby(['id' => SORT_ASC])
            ->asArray()
            ->all();
    }
}
