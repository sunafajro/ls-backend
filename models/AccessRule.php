<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_accessrules".
 *
 * @property integer $id
 * @property string $action
 * @property string $controller
 * @property integer $role
 * @property integer $visible
 */

class AccessRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_accessrule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action', 'controller', 'role', 'visible'], 'required'],
            [['action', 'controller'], 'string'],
            [['role', 'visible'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action' => Yii::t('app', 'Action'),
            'controller' => Yii::t('app', 'Controller'),
            'role' => Yii::t('app', 'Role'),
        ];
    }

        /**
     * Метод возвращает список доступных ролей пользователей в виде многомерного массива.
     * @return mixed
     */
    public static function getRulesList()
    {
        $rules = (new \yii\db\Query())
        ->select(['id' => 'r.id', 'action' => 'r.action', 'controller' => 'r.controller', 'role_id' => 'r.role', 'role' => 's.name'])
        ->from(static::tableName() . ' r')
        ->innerJoin('status s', 's.id=r.role')
        ->where('r.visible=:one', [':one' => 1])
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
                ]
            ],
            'data'    => $rules
        ];
    }

    public static function GetCRUD($controller)
    {
        return [
            'create' => static::CheckAccess($controller, 'create'),
            'update' => static::CheckAccess($controller, 'update'),
            'delete' => static::CheckAccess($controller, 'delete'),
        ];
    }

    public static function CheckAccess($controller = NULL, $action = NULL)
    {
        $result = NULL;
        if ($controller && $action) {
            $result = AccessRule::find()->where('action=:action AND controller=:controller AND role=:role', [':action' => $action, ':controller' => $controller, ':role' => Yii::$app->session->get('user.ustatus')])->one();
        }
        return $result ? true : false;
    }
}