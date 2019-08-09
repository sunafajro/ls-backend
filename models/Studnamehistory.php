<?php

namespace app\models;

use app\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "calc_studnamehistory".
 *
 * @property integer $id
 * @property integer $user
 * @property string $data
 * @property integer $calc_studname
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property integer $calc_cumulativediscount
 * @property integer $active
 * @property integer $calc_way
 */
class Studnamehistory extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_studnamehistory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user', 'data', 'calc_studname', 'name', 'phone', 'email', 'calc_cumulativediscount', 'active', 'calc_way'], 'required'],
            [['user', 'calc_studname', 'calc_cumulativediscount', 'active', 'calc_way'], 'integer'],
            [['data'], 'safe'],
            [['name', 'phone', 'email'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'User'),
            'data' => Yii::t('app', 'Data'),
            'calc_studname' => Yii::t('app', 'Calc Studname'),
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'calc_cumulativediscount' => Yii::t('app', 'Calc Cumulativediscount'),
            'active' => Yii::t('app', 'Active'),
            'calc_way' => Yii::t('app', 'Calc Way'),
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
