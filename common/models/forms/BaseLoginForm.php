<?php

namespace common\models\forms;

use common\models\BaseAuth;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * Class LoginForm
 * @package common\models\forms
 *
 * @property string $username
 * @property string $password
 * @property bool $rememberMe
 * @property-read BaseAuth|false $user
 *
 */
class BaseLoginForm extends Model
{
    /** @var string */
    public $username;
    /** @var string */
    public $password;
    /** @var bool */
    public $rememberMe = true;
    /** @var BaseAuth|false  */
    protected $_user = false;


    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'rememberMe' => Yii::t('app', 'Remember me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * @return BaseAuth|false
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = BaseAuth::findByUsername($this->username);
        }

        return $this->_user;
    }
}