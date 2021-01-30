<?php

namespace app\modules\school\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "access_rules".
 *
 * @property integer $id
 * @property string  $action
 * @property string  $controller
 * @property integer $role_id
 * @property integer $visible
 */
class AccessRule extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'access_rules';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['action', 'controller'], 'string'],
            [['role_id',   'visible'], 'integer'],
            [['action', 'controller', 'role_id', 'visible'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'action'     => Yii::t('app', 'Action'),
            'controller' => Yii::t('app', 'Controller'),
            'role_id'    => Yii::t('app', 'Role'),
        ];
    }

    /**
     * Метод возвращает список доступных ролей пользователей в виде многомерного массива.
     * 
     * @return array
     */
    public static function getRulesList()
    {
        $rules = (new \yii\db\Query())
        ->select([
            'id'         => 'r.id',
            'action'     => 'r.action',
            'controller' => 'r.controller',
            'role_id'    => 'r.role_id',
            'role'       => 's.name',
            'moduleType' => 'r.module_type',
        ])
        ->from(['r' => self::tableName()])
        ->innerJoin(['s' => Role::tableName()], 's.id = r.role_id')
        ->where(['r.visible' => 1])
        ->orderby(['r.id' => SORT_ASC])
        ->all();

        return [
            'columns' => [
                [
                    'id'   => 'id',
                    'name' => '№',
                    'show' => true
                ],
                [
                    'id'   => 'action',
                    'name' => Yii::t('app', 'Action'),
                    'show' => true
                ],
                [
                    'id'   => 'controller',
                    'name' => Yii::t('app', 'Controller'),
                    'show' => true
                ],
                [
                    'id'   => 'role_id',
                    'name' => Yii::t('app', 'Role ID'),
                    'show' => false
                ],
                [
                    'id'   => 'role',
                    'name' => Yii::t('app', 'Role'),
                    'show' => true
                ],
                [
                    'id'   => 'moduleType',
                    'name' => Yii::t('app', 'Module'),
                    'show' => true
                ],
            ],
            'data'    => $rules
        ];
    }

    /**
     * @param string $controller
     * @return array
     */
    public static function getCRUD(string $controller): array
    {
        return [
            'create' => self::checkAccess($controller, 'create'),
            'update' => self::checkAccess($controller, 'update'),
            'delete' => self::checkAccess($controller, 'delete'),
        ];
    }

    /**
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public static function checkAccess(string $controller, string $action): bool
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;

        $result = NULL;
        if ($controller && $action) {
            $result = self::find()->where([
                'action'     => $action,
                'controller' => $controller,
                'role_id'    => $auth->roleId,
            ])
            ->one();
        }
        return $result ? true : false;
    }
}