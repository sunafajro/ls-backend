<?php

namespace app\models;

use yii\base\BaseObject;
use yii\web\IdentityInterface;

/**
 * Class BaseAuth
 * @package app\models
 *
 * @property int    $id
 * @property string $username
 * @property string $password
 * @property string $authKey
 * @property string $accessToken
 */
class BaseAuth extends BaseObject implements IdentityInterface
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
        $user = BaseUser::findBy('id', $id);

        return !empty($user) ? new static($user) : null;
    }

    /**
     * Finds user by username
     * @param string $username

     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = BaseUser::findBy('login', $username);

        return !empty($user) ? new static($user) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = BaseUser::findBy('access_token', $token);

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
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
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