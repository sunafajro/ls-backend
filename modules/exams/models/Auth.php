<?php

namespace app\modules\exams\models;

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
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $user = User::findBy(User::tableName() . '.id', $id);

        return !empty($user) ? new static($user) : null;
    }

    /**
     * Finds user by username
     * @param string $username

     * @return static|null
     */
    public static function findByUsername($username)
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