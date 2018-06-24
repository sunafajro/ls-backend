<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_langteacher".
 *
 * @property integer $id
 * @property integer $calc_teacher
 * @property integer $calc_lang
 * @property integer $visible
 * @property string $data
 * @property integer $user
 */
class Langteacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_langteacher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_teacher', 'calc_lang', 'visible', 'data', 'user'], 'required'],
            [['calc_teacher', 'calc_lang', 'visible', 'user'], 'integer'],
            [['data'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calc_teacher' => 'Calc Teacher',
            'calc_lang' => 'Calc Lang',
            'visible' => 'Visible',
            'data' => 'Data',
            'user' => 'User',
        ];
    }
}
