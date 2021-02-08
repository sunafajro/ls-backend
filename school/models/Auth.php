<?php

namespace school\models;

/**
 * Class Auth
 * @package school\models
 *
 * @property string $fullName   Полное имя пользователя
 * @property int    $roleId     ID роли пользователя
 * @property string $roleName   Наименование роли пользователя
 * @property int    $teacherId  ID преподавателя (для пользователей связанных с сущностью Преподаватель)
 * @property int    $officeId   ID офиса за которым закреплен пользователь (для менеджеров)
 * @property string $officeName Наименование офиса за которым закреплен пользователь (для менеджеров)
 * @property int    $cityId     ID города офиса за которым закреплен пользователь (для менеджеров)
 * @property string $cityName   Наименование города офиса за которым закреплен пользователь (для менеджеров)
 */
class Auth extends \common\models\Auth
{
    /** @var string */
    public $fullName;
    /** @var int */
    public $roleId;
    /** @var string */
    public $roleName;
    /** @var int */
    public $teacherId;
    /** @var int */
    public $officeId;
    /** @var string */
    public $officeName;
    /**@var string*/
    public $cityId;
    /**@var string*/
    public $cityName;

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $user = User::findBy(User::tableName() . '.id', $id);

        return !empty($user) ? new static($user) : null;
    }

    /**
     * @param string $username
     * @return Auth|null
     */
    public static function findByUsername(string $username)
    {
        $user = User::findBy(User::tableName() . '.login', $username);

        return !empty($user) ? new static($user) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = User::findBy(User::tableName() . '.access_token', $token);

        return !empty($user) ? new static($user) : null;
    }
}