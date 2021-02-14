<?php

namespace school\models;

use school\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "calc_sms_log".
 *
 * @property integer $id
 * @property string $date
 * @property integer $user
 * @property integer $calc_studname
 * @property string $phone
 * @property integer $code
 * @property string $result
 */
class Smslog extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_sms_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'user', 'phone', 'code', 'result'], 'required'],
            [['date'], 'safe'],
            [['user', 'calc_studname', 'code'], 'integer'],
            [['phone'], 'string', 'max' => 11],
            [['result'], 'string', 'max' => 255],
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
            'user' => Yii::t('app', 'User'),
            'calc_studname' => Yii::t('app', 'Calc Studname'),
            'phone' => Yii::t('app', 'Phone'),
            'code' => Yii::t('app', 'Code'),
            'result' => Yii::t('app', 'Result'),
        ];
    }
    
    /**
     * @deprecated
     * метод подменяет в строках идентификатор одного студента на идентификатор другого
     * @param integer @id1
     * @param integer @id2
     * @return boolean
     */
    public static function changeStudentId($id1, $id2)
    {
        $sql = (new \yii\db\Query())
        ->createCommand()
        ->update(self::tableName(), ['calc_studname' => $id1], ['calc_studname' => $id2])
        ->execute();

        return ($sql == 0) ? false : true;
    }
}
