<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_login_log".
 *
 * @property integer $id
 * @property string $date
 * @property integer $result
 * @property integer $user_id
 * @property string $ipaddr
 */
class LoginLog extends \yii\db\ActiveRecord
{
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
            [['date', 'result', 'user_id', 'ipaddr'], 'required'],
            [['date'], 'safe'],
            [['result', 'user_id'], 'integer'],
            [['ipaddr'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Date'),
            'result' => Yii::t('app', 'Result'),
            'user_id' => Yii::t('app', 'User ID'),
            'ipaddr' => Yii::t('app', 'Ipaddr'),
        ];
    }
}
