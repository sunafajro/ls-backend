<?php

namespace client\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property Auth|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
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
                $this->addError($attribute, Yii::t('app', 'Incorrect username or password'));
            }
            // @deprecated
            // if (!$this->hasErrors()) {
                // ограничение на вход при неактивности более 2 месяцев
                // $loginLimitDate = date('Y-m-d', strtotime('-2 month'));
                // if ($user->lastLoginDate < $loginLimitDate || (int)$user->isActive === 0) {
                //     $this->addError('date', Yii::t('app', 'Access for your account was restricted. Please contact your office manager.'));
                // }
            // }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }
        $user = $this->getUser();
        if (empty($user)) {
            return false;
        }
        // @deprecated
        // $client = Client::find()->where(['calc_studname' => $user->id])->one();
        // if (empty($client)) {
        //     return false;
        // }
        // $client->date = date('Y-m-d');
        // if (!$client->save(true, ['date'])) {
        //     return false;
        // }
        return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
    }

    /**
     * Finds user by [[username]]
     *
     * @return Auth|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Auth::findByUsername($this->username);
        }

        return $this->_user;
    }
}
