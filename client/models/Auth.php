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

    /** @var User */
    private $_user;

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $student = new Student();
        $client = $student->findByIdOrUsername($id, null);
        $auth = $client ? new static($client) : null;
        if (!is_null($auth)) {
            $auth->_user = User::find()->andWhere([
                'calc_studname' => $auth->id,
                'site'          => 1,
            ])->one();
        }
        return $auth;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /** @var User $user */
        $user = User::find()->where([
            'access_token' => $token,
            'site'         => 1,
        ])->one();
        if (!empty($user)) {
            $student = new Student();
            $client = $student->findByIdOrUsername($user->calc_studname, $user->username);
            $auth = $client ? new static($client) : null;
            if (!is_null($auth)) {
                $auth->_user = $user;
            }

            return $auth;
        }

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
        $auth = $client ? new static($client) : null;
        if (!is_null($auth)) {
            $auth->_user = User::find()->andWhere([
                'calc_studname' => $auth->id,
                'site'          => 1,
            ])->one();
        }
        return $auth;
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
        // TODO реализовать генерацию и использование ключа
        // $this->authKey;
        return null;
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

    /**
     * Resets access token
     * @throws \yii\base\Exception
     */
    public function resetAccessToken(): bool
    {
        if ($this->_user->resetAccessToken()) {
            $this->accessToken = $this->_user->access_token;
            return true;
        }

        return false;
    }
}
