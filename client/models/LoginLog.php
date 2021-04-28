<?php

namespace client\models;

use common\models\BaseLoginLog;
use client\Client;

/**
 * This is the model class for table "login_logs".
 *
 * @property integer $id
 * @property string  $date
 * @property integer $result
 * @property string  $module_type
 * @property integer $user_id
 * @property string  $ipaddr
 */
class LoginLog extends BaseLoginLog
{
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = parent::rules();
        array_unshift($rules, [['module_type'], 'default', 'value' => Client::MODULE_NAME]);
        return $rules;
    }
}
