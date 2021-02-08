<?php

namespace school\models;

use school\School;

/**
 * Class LoginLog
 * @package school\models
 *
 * @property integer $id
 * @property string  $date
 * @property integer $result
 * @property string  $module_type
 * @property integer $user_id
 * @property string  $ipaddr
 */
class LoginLog extends \common\models\LoginLog
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        $rules = parent::rules();
        array_unshift($rules, [['module_type'], 'default', 'value' => School::MODULE_SLUG]);
        return $rules;
    }
}
