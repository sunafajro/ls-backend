<?php

namespace school\models;

use school\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "calc_studphone".
 *
 * @property integer $id
 * @property integer $visible
 * @property integer $calc_studname
 * @property string $phone
 * @property string $description
 * @property string $create_date
 * @property integer $create_user
 * @property integer $type
 * @property string $delete_date
 * @property integer $delete_user
 */
class Studphone extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_studphone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible', 'calc_studname', 'phone', 'create_date', 'create_user', 'type'], 'required'],
            [['visible', 'calc_studname', 'create_user', 'type', 'delete_user'], 'integer'],
            [['create_date', 'delete_date'], 'safe'],
            [['phone', 'description'], 'string', 'max' => 255]
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
            'calc_studname' => Yii::t('app', 'Calc Studname'),
            'phone' => Yii::t('app', 'Phone'),
            'description' => Yii::t('app', 'Description'),
            'create_date' => Yii::t('app', 'Create Date'),
            'create_user' => Yii::t('app', 'Create User'),
            'type' => Yii::t('app', 'Type'),
            'delete_date' => Yii::t('app', 'Delete Date'),
            'delete_user' => Yii::t('app', 'Delete User'),
        ];
    }
    
    /**
     *  метод отдает список телефонов студента по его id
     */
    public static function getStudentPhoneById($sid) 
    {
        $phones = self::find()
        ->select(['id'=> 'id', 'phone' => 'phone', 'description' => 'description'])
        ->where('visible=:one', [':one'=>1])
        ->andWhere(['calc_studname'=>$sid])
        ->all();
        
        return ($phones === NULL) ? [] : $phones;
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
