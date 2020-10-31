<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "roles".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $description
 * @property integer $visible
 * @property string  $module_type
 */
class BaseRole extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['name', 'description', 'module_type'], 'string'],
            [['visible'], 'integer'],
            [['name', 'description', 'visible', 'module_type'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => Yii::t('app', 'Role name'),
            'description' => Yii::t('app', 'Description'),
            'visible'     => Yii::t('app', 'Visible'),
            'module_type' => Yii::t('app', 'Module'),
        ];
    }

    /**
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
            ->where(['visible' => 1])
            ->orderby(['id' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     * @return array
     */
    public static function getRolesListSimple() : array
    {
        return ArrayHelper::map(static::getRolesList(), 'id', 'name');
    }
}
