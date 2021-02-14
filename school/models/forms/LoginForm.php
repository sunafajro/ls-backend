<?php

namespace school\models\forms;

use common\models\forms\BaseLoginForm;
use school\models\Auth;

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
