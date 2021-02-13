<?php

namespace exam\models\forms;

use common\models\forms\BaseLoginForm;
use exam\models\Auth;

/**
 * LoginForm is the model behind the login form.
 *
 * @property Auth|null $user This property is read-only.
 */
class LoginForm extends BaseLoginForm
{
    private $_user = false;

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
