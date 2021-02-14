<?php

namespace client\models;

/**
 * Class Auth
 * @package client\models
 */
class Auth extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $name;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $isActive;
    public $lastLoginDate;

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $student = new Student();
        $client = $student->findByIdOrUsername($id, null);
        return $client ? new static($client) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO написать механизм создания и хранения токена
        // foreach (self::$users as $user) {
        //     if ($user['accessToken'] === $token) {
        //         return new static($user);
        //     }
        // }
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $student = new Student();
        $client = $student->findByIdOrUsername(null, $username);
        return $client ? new static($client) : null;
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
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        // TODO написать механизм создания и хранения ключа
        // return $this->authKey === $authKey;
        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === md5($password);
    }
}
