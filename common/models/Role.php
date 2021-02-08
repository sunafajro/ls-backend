<?php

namespace common\models;

use common\models\queries\RoleQuery;
use Yii;

/**
 * Class Role
 * @package common\models
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $visible
 * @property string $module_type
 */
class Role extends ActiveRecord
{
    const DEFAULT_FIND_CLASS = RoleQuery::class;
    const DEFAULT_MODULE_TYPE = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%roles}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['module_type'], 'default', 'value' => static::DEFAULT_MODULE_TYPE],
            [['name', 'description', 'module_type'], 'string'],
            [['visible'], 'integer'],
            [['name', 'description', 'visible', 'module_type'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Role name'),
            'description' => Yii::t('app', 'Description'),
            'visible' => Yii::t('app', 'Visible'),
            'module_type' => Yii::t('app', 'Module'),
        ];
    }

    /**
     * @return RoleQuery
     */
    public static function find(): RoleQuery
    {
        $findClass = static::DEFAULT_FIND_CLASS;
        $findQuery = new $findClass(get_called_class(), []);

        return static::addDefaultFindCondition($findQuery);
    }

    /**
     * @param RoleQuery $query
     * @return RoleQuery
     */
    public static function addDefaultFindCondition(RoleQuery $query): RoleQuery
    {
        if (static::DEFAULT_MODULE_TYPE) {
            $query->byModuleType(static::DEFAULT_MODULE_TYPE);
        }
        return $query;
    }
}