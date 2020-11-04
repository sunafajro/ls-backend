<?php

namespace app\modules\exams\models;

use app\models\BaseUser;
use app\models\queries\BaseUserQuery;
use app\modules\exams\Exams;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class User
 * @package app\modules\exams\models
 *
 * @property integer $id
 * @property integer $visible
 * @property string  $name
 * @property string  $login
 * @property string  $pass
 * @property integer $status
 */
class User extends BaseUser
{
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
     * {@inheritDoc}
     */
    public static function findUserByCondition(array $condition, bool $onlyActive = true)
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
     * @return BaseUserQuery | ActiveQuery
     */
    public static function find() : ActiveQuery
    {
        $query = parent::find();
        return $query->andWhere(['module_type' => Exams::MODULE_NAME]);
    }
}