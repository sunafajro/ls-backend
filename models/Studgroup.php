<?php

namespace app\models;

use app\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "calc_studgroup".
 *
 * @property integer $id
 * @property integer $visible
 * @property integer $user
 * @property string $data
 * @property integer $calc_studname
 * @property integer $calc_groupteacher
 * @property integer $user_visible
 * @property string $data_visible
 * @property integer $captain
 */
class Studgroup extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_studgroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible', 'user', 'data', 'calc_studname', 'calc_groupteacher'], 'required'],
            [['visible', 'user', 'calc_studname', 'calc_groupteacher', 'user_visible', 'captain'], 'integer'],
            [['data', 'data_visible'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'visible' => Yii::t('app', 'Visible'),
            'user' => Yii::t('app', 'User'),
            'data' => Yii::t('app', 'Date'),
            'calc_studname' => Yii::t('app', 'Student'),
            'calc_groupteacher' => Yii::t('app', 'Group'),
            'user_visible' => Yii::t('app', 'User Visible'),
            'data_visible' => Yii::t('app', 'Data Visible'),
            'captain' => Yii::t('app', 'Captain'),
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
