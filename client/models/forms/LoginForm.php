<?php

namespace client\models\forms;

use client\models\Auth;
use common\models\forms\BaseLoginForm;

/**
 * @property-read Auth|false $user
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
