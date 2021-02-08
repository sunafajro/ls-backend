<?php

namespace school\models\forms;

use school\models\Auth;

/**
 * Class LoginForm
 * @package school\models\forms
 *
 * @property-read Auth|null $user
 * @method Auth|null getUser()
 */
class LoginForm extends \common\models\forms\LoginForm
{
    const DEFAULT_AUTH_CLASS = Auth::class;
}