<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

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
class BaseLoginLog extends ActiveRecord
{
    const ACTION_LOGIN  = 1;
    const ACTION_LOGOUT = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_login_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'default', 'value' => Yii::$app->user->identity->id],
            [['date'],    'default', 'value' => date('Y-m-d H:i:s')],
            [['result', 'user_id'], 'integer'],
            [['module_type'], 'string'],
            [['ipaddr'], 'string', 'max' => 128],
            [['date'], 'safe'],
            [['date', 'result', 'user_id', 'ipaddr', 'module_type'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'date'        => Yii::t('app', 'Date'),
            'result'      => Yii::t('app', 'Result'),
            'module_type' => Yii::t('app', 'Module type'),
            'user_id'     => Yii::t('app', 'User ID'),
            'ipaddr'      => Yii::t('app', 'Ipaddr'),
        ];
    }
}
