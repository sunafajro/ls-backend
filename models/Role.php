<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $visible
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'visible'], 'required'],
            [['name', 'description'], 'string'],
            [['visible'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Role name'),
            'description' => Yii::t('app', 'Description'),
            'visible' => 'Visible',
        ];
    }

    /**
     * Метод возвращает список доступных ролей пользователей в виде многомерного массива.
     * @return mixed
     */
    public static function getRolesList()
    {
        $roles = (new \yii\db\Query())
        ->select('id as id, name as name, description as description')
        ->from('status')
        ->where('visible=:one', [':one' => 1])
        ->orderby(['id' => SORT_ASC])
        ->all();

        return $roles; 
    }

    /**
     * Метод возвращает список доступных ролей пользователей в виде одномерного массива.
     * @return mixed
     */
    public static function getRolesListSimple()
    {
        $tmp_statuses = static::getRolesList();
        $statuses = [];

        if(!empty($tmp_statuses)) {
            foreach($tmp_statuses as $s) {
                $statuses[$s['id']] = $s['name'];
            }
        }

        return $statuses;
    }
}
