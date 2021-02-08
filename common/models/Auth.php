<?php

namespace common\models;

use yii\base\BaseObject;
use yii\web\IdentityInterface;

/**
 * Class Auth
 * @package common\models
 */
class Auth extends BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $user = User::findBy('id', $id);

        return !empty($user) ? new static($user) : null;
    }

    /**
     * @param string $username
     * @return Auth|null
     */
    public static function findByUsername(string $username)
    {
        $user = User::findBy('username', $username);

        return !empty($user) ? new static($user) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = User::findBy('access_token', $token);

        return !empty($user) ? new static($user) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->authKey === $authKey;
    }

    /**
     * TODO переделать под использование более строгих алгоритмов
     * @param string $password
     *
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return $this->password === md5($password);
    }
}