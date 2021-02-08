<?php

namespace common\models\forms;

use common\models\Auth;
use Yii;
use yii\base\Model;

/**
 * Class LoginForm
 * @package common\models
 *
 * @property-read Auth|null $user
 */
class LoginForm extends Model
{
    const DEFAULT_AUTH_CLASS = Auth::class;

    /** @var string */
    public $username;
    /** @var string */
    public $password;
    /** @var bool */
    public $rememberMe = true;

    /** @var bool|Auth  */
    private $_user = false;


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
        ];
    }

    /**
     * @param string $attribute
     * @param array $params
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
     * @return bool
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * @return Auth|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $class = static::DEFAULT_AUTH_CLASS;
            $this->_user = call_user_func("{$class}::findByUsername", $this->username);
        }

        return $this->_user;
    }
}