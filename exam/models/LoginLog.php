<?php

namespace exam\models;

use common\models\BaseLoginLog;
use exam\Exam;

/**
 * This is the model class for table "calc_login_log".
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
    public function rules()
    {
        $rules = parent::rules();
        array_unshift($rules, [['module_type'], 'default', 'value' => Exams::MODULE_NAME]);
        return $rules;
    }
}
