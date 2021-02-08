<?php

namespace common\models;

use Yii;

/**
 * Class LoginLog
 * @package common\models
 *
 * @property integer $id
 * @property string $date
 * @property integer $result
 * @property string $module_type
 * @property integer $user_id
 * @property string $ip_address
 */
class LoginLog extends \yii\db\ActiveRecord
{
    const ACTION_LOGIN = 1;
    const ACTION_LOGOUT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%login_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id'], 'default', 'value' => Yii::$app->user->identity->id],
            [['date'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['result', 'user_id'], 'integer'],
            [['module_type'], 'string'],
            [['ip_address'], 'string'],
            [['date'], 'safe'],
            [['date', 'result', 'user_id', 'ip_address', 'module_type'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Date'),
            'result' => Yii::t('app', 'Result'),
            'module_type' => Yii::t('app', 'Module type'),
            'user_id' => Yii::t('app', 'User ID'),
            'ip_address' => Yii::t('app', 'IP address'),
        ];
    }
}
