<?php

namespace school\models;

use school\models\queries\UserQuery;
use school\School;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class User
 * @package school\models
 *
 * @property integer $id           ID пользователя
 * @property integer $site         Доступ к сайту @deprecated
 * @property integer $visible      Статус пользователя (1 - действующий, 0 - удален)
 * @property string $login        Логин пользователя
 * @property string $pass         Пароль md5 хеш пароля пользователя
 * @property string $name         ФИО пользователя
 * @property integer $status       ID роли пользователя
 * @property integer $calc_office  ID офиса за которым закреплен пользователь (для менеджеров)
 * @property integer $calc_teacher ID преподавателя (для пользователей связанных с сущностью Преподаватель)
 * @property integer $calc_city    ID города офиса за которым закреплен пользователь (для менеджеров)
 * @property string $logo         Фото пользователя
 * @property string $module_type  Slug модуля приложения
 *
 * @property City $city
 * @property Office $office
 * @property Role $role
 * @property Teacher $teacher
 */
class User extends \common\models\User
{
    const DEFAULT_FIND_CLASS = UserQuery::class;
    const DEFAULT_MODULE_TYPE = School::MODULE_SLUG;

    /** @deprecated Убрать. Реализовать в форме UserForm */
    public $pass_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['city_id', 'office_id', 'teacher_id', 'site'], 'default', 'value' => 0],
            [['logo'], 'default', 'value' => ''],
            [['module_type'], 'default', 'value' => self::DEFAULT_MODULE_TYPE],
            [['site', 'visible', 'role_id', 'office_id', 'teacher_id', 'city_id'], 'integer'],
            [['username', 'password', 'name', 'logo', 'module_type'], 'string'],
            [['username'], 'unique', 'on' => 'create',
                'when' => function ($model) {
                    return self::findBy(self::tableName() . '.username', $model->username) !== NULL;
                }],
            [['username'], 'unique', 'on' => 'update',
                'when' => function ($model) {
                    return self::getUserName($model->id) !== $model->login;
                }],
            [['username', 'name'], 'string', 'min' => 3],
            [['password'], 'string', 'min' => 8],
            [['visible', 'username', 'password', 'name', 'role_id', 'module_type'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'site' => \Yii::t('app', 'Site access'),
            'visible' => \Yii::t('app', 'Active'),
            'username' => \Yii::t('app', 'User login'),
            'password' => \Yii::t('app', 'Password'),
            'name' => \Yii::t('app', 'Full name'),
            'role_id' => \Yii::t('app', 'Role ID'),
            'office_id' => \Yii::t('app', 'Office ID'),
            'teacher_id' => \Yii::t('app', 'Teacher ID'),
            'city_id' => \Yii::t('app', 'City ID'),
            'logo' => \Yii::t('app', 'User logo'),
            'module_type' => \Yii::t('app', 'Module name'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function findUserByCondition(array $condition, bool $onlyActive = true): ?array
    {
        $userData = self::find()
            ->select([
                'id' => 'users.id',
                'username' => 'users.username',
                'password' => 'users.password',
                'fullName' => 'users.name',
                'roleId' => 'roles.id',
                'roleName' => 'roles.name',
                'teacherId' => 'users.teacher_id',
                'officeId' => 'offices.id',
                'officeName' => 'offices.name',
                'cityId' => 'cities.id',
                'cityName' => 'cities.name',
            ])
            ->innerJoin(['roles' => Role::tableName()], "roles.id = users.role_id")
            ->leftJoin(['offices' => Office::tableName()], "offices.id = users.office_id")
            ->leftJoin(['cities' => City::tableName()], "cities.id = offices.city_id")
            ->where([
                'users.visible' => 1,
                'users.module_type' => self::DEFAULT_MODULE_TYPE,
            ])
            ->andWhere($condition)
            ->asArray()
            ->one();

        // принудительное приведение типов
        foreach ($userData ?? [] as $key => $value) {
            if (in_array($key, ['id', 'roleId', 'teacherId', 'officeId', 'cityId']) && !empty($value)) {
                $userData[$key] = intval($value);
            }
        }

        return $userData;
    }

    /**
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOffice(): ActiveQuery
    {
        return $this->hasOne(Office::class, ['id' => 'office_id']);
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
    public function getTeacher(): ActiveQuery
    {
        return $this->hasOne(Teacher::class, ['id' => 'teacher_id']);
    }

    /**
     * возвращает данные пользователя, для информационного блока
     *
     * @return array
     */
    public static function getUserInfo() : array
    {
        $userData = [];

        $userData['name'] = Yii::$app->user->identity->fullName;
        if (Yii::$app->user->identity->teacherId) {
            $userData['teacherId'] = Yii::$app->user->identity->teacherId;
        } else {
            $userData['teacherId'] = null;
        }
        $userData['roleId'] = Yii::$app->user->identity->roleId;
        $userData['role'] = Yii::$app->user->identity->roleName;
        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
            $userData['office'] = Yii::$app->user->identity->officeName;
            $userData['officeId'] = Yii::$app->user->identity->officeId;
        } else {
            $userData['office'] = null;
        }

        return $userData;
    }
}