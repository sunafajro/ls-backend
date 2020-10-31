<?php

namespace app\modules\exams\models;

use app\models\BaseUser;
use app\modules\exams\Exams;
use Yii;

class User extends BaseUser
{
    /** @deprecated Убрать. Реализовать в форме UserForm */
    public $pass_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['site'], 'default', 'value' => 0],
            [['module_type'], 'default', 'value' => Exams::MODULE_NAME],
            [['site', 'visible', 'status', 'calc_office', 'calc_teacher', 'calc_city'], 'integer'],
            [['login', 'pass', 'name', 'logo', 'module_type'], 'string'],
            [['login'], 'unique', 'on' => 'create',
                'when' => function ($model) {
                    return self::findBy(self::tableName() . '.login', $model->login) !== NULL;
                }],
            [['login'], 'unique', 'on' => 'update',
                'when' => function ($model) {
                    return self::getUserName($model->id) !== $model->login;
                }],
            [['login', 'name'], 'string', 'min' => 3],
            [['pass'], 'string', 'min' => 8],
            [['visible', 'login', 'pass', 'name', 'status', 'module_type'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'id'      => 'ID',
            'site'    => 'Site',
            'visible' => Yii::t('app','Active'),
            'login'   => Yii::t('app','User login'),
            'pass'    => Yii::t('app','Password'),
            'name'    => Yii::t('app','Full name'),
            'status'  => Yii::t('app','Role'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->visible = 0;

        return $this->save(true, ['visible']);
    }

    /**
     * @param array $condition
     *
     * @return array
     */
    public static function findUserByCondition(array $condition)
    {
        return self::find()
            ->select([
                'id'         => 'users.id',
                'username'   => 'users.login',
                'password'   => 'users.pass',
                'fullName'   => 'users.name',
                'roleId'     => 'roles.id',
                'roleName'   => 'roles.name',
            ])
            ->innerJoin(['roles'  => Role::tableName()], "roles.id = users.status")
            ->where([
                'users.visible'     => 1,
                'users.module_type' => Exams::MODULE_NAME,
            ])
            ->andWhere($condition)
            ->asArray()
            ->one();
    }

    /**
     * @param int $id
     * @return string|null
     */
    public static function getUserName(int $id)
    {
        return self::findBy(self::tableName() . '.id', $id)['username'] ?? null;
    }
}