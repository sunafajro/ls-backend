<?php

namespace app\models;

use Yii;
use app\models\User;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $authKey
 * @property string $accessToken
 */
class Auth extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    // public $authKey;
    // public $accessToken;

    // private static $user;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        if (($user = User::findUserById($id)) !== NULL) {
            // $user['authKey']     = NULL;
            // $user['accessToken'] = NULL;
            return new static($user);
        } else {
            return NULL;
        }
    }

    public static function findByUsername($username)
    {
        if (($user = User::findUserByUsername($username)) !== NULL) {            
            // $user['authKey']     = NULL;
            // $user['accessToken'] = NULL;
            return new static($user);
        } else {
            return NULL;
        }
    }
    
    public function validatePassword($password)
    {
        return $this->password === md5(trim($password));
    }

    /* TODO доработать!!! */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
