<?php

namespace app\modules\school\models;

use app\models\BaseAuth;

class Auth extends BaseAuth
{
    /**
     * Полное имя пользователя
     * @var string
     */
    public $fullName;
    /**
     * ID роли пользователя
     * @var int
     */
    public $roleId;
    /**
     * Наименование роли пользователя
     * @var string
     */
    public $roleName;
    /**
     * ID преподавателя (для пользователей связанных с сущностью Преподаватель)
     * @var int
     */
    public $teacherId;
    /**
     * ID офиса за которым закреплен пользователь (для менеджеров)
     * @var int
     */
    public $officeId;
    /**
     * Наименование офиса за которым закреплен пользователь (для менеджеров)
     * @var string
     */
    public $officeName;
    /**
     * ID города офиса за которым закреплен пользователь (для менеджеров)
     * @var string
     */
    public $cityId;
    /**
     * Наименование города офиса за которым закреплен пользователь (для менеджеров)
     * @var string
     */
    public $cityName;

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $user = User::findUserById($id);

        return !empty($user) ? new static($user) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findByUsername($username)
    {
        $user = User::findUserByUsername($username);
        if (strcasecmp($user['username'], $username) === 0) {
            return new static($user);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO написать механизм создания и хранения токена
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        // TODO написать механизм создания и хранения ключа
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validatePassword($password)
    {
        return $this->password === md5($password);
    }
}