<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_stud_login_log".
 *
 * @property integer $id
 * @property string $date
 * @property integer $calc_studname
 */
class Studloginlog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_stud_login_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'calc_studname'], 'required'],
            [['date'], 'safe'],
            [['calc_studname'], 'integer'],
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
            'calc_studname' => Yii::t('app', 'Calc Studname'),
        ];
    }
    /**
     *  метод подменяет в строках идентификатор одного студента на идентификатор другого
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
