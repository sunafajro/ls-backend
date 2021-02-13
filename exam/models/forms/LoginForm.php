<?php

namespace exam\models\forms;

use common\models\forms\BaseLoginForm;
use exam\models\Auth;

/**
 * @property Auth|false $user
 */
class LoginForm extends BaseLoginForm
{
    /**
     * @return Auth|false
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Auth::findByUsername($this->username);
        }

        return $this->_user;
    }
}
