<?php

namespace app\models;

use app\models\queries\BaseRoleQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
    const DEFAULT_FIND_CLASS = BaseRoleQuery::class;
    const DEFAULT_MODULE_TYPE = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
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
    public function attributeLabels(): array
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
     * @return BaseRoleQuery|ActiveQuery
     */
    public static function find() : ActiveQuery
    {
        $findClass = static::DEFAULT_FIND_CLASS;
        $findCondition = static::getDefaultFindCondition();
        $findQuery = new $findClass(get_called_class(), []);

        return $findQuery->andFilterWhere($findCondition);
    }

    /**
     * @return array
     */
    public static function getDefaultFindCondition(): array
    {
        $condition = [];
        if (static::DEFAULT_MODULE_TYPE) {
            $condition['module_type'] = static::DEFAULT_MODULE_TYPE;
        }

        return $condition;
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }
}
