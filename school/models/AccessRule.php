<?php

namespace school\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "access_rules".
 *
 * @property integer $id
 * @property string  $controller
 * @property string  $action
 * @property integer $role_id
 * @property integer $user_id
 *
 * @property-read Role $role
 * @property-read User $user
 */
class AccessRule extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%access_rules}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['action', 'controller'], 'string'],
            [['role_id', 'user_id'], 'integer'],
            [['action', 'controller'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'controller' => Yii::t('app', 'Controller'),
            'action'     => Yii::t('app', 'Action'),
            'role_id'    => Yii::t('app', 'Role'),
            'user_id'    => Yii::t('app', 'User'),
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

        return self::find()->where([
                'action'     => $action,
                'controller' => $controller,
            ])
            ->andWhere(['or', ['role_id' => $auth->roleId], ['user_id' => $auth->id]])
            ->exists();
    }

    /**
     * @return ActiveQuery
     */
    public function getRole(): ActiveQuery
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}