<?php

namespace school\models;

use Yii;

/**
 * This is the model class for table "calc_teachergroup".
 *
 * @property integer $id
 * @property integer $calc_teacher
 * @property integer $calc_groupteacher
 * @property integer $visible
 * @property integer $user
 * @property string $date
 */
class Teachergroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_teachergroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_teacher', 'calc_groupteacher', 'visible', 'user', 'date'], 'required'],
            [['calc_teacher', 'calc_groupteacher', 'visible', 'user'], 'integer'],
            [['date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'calc_teacher' => Yii::t('app', 'Teacher'),
            'calc_groupteacher' => Yii::t('app', 'Group'),
            'visible' => Yii::t('app', 'Visible'),
            'user' => Yii::t('app', 'User'),
            'date' => Yii::t('app', 'Date'),
        ];
    }
}
