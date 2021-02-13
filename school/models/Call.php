<?php

namespace school\models;

use school\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "calc_call".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $description
 * @property integer $visible
 * @property integer $ok
 * @property integer $user_ok
 * @property string $data_ok
 * @property string $phone
 * @property integer $calc_sex
 * @property integer $calc_servicetype
 * @property integer $calc_lang
 * @property integer $calc_eduform
 * @property integer $calc_service
 * @property integer $calc_office
 * @property integer $calc_edulevel
 * @property integer $calc_eduage
 * @property integer $calc_class
 * @property integer $calc_nomination
 * @property integer $calc_way
 * @property integer $user
 * @property string $data
 * @property integer $flag_check
 * @property integer $user_check
 * @property string $data_check
 * @property integer $transform
 * @property integer $user_transform
 * @property string $data_transform
 * @property integer $calc_studname
 * @property integer $user_edit
 * @property string $data_edit
 * @property integer $user_visible
 * @property string $data_visible
 */
class Call extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_call';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'visible', 'phone', 'calc_sex', 'calc_servicetype', 'calc_lang', 'calc_eduage', 'calc_way', 'user', 'data'], 'required'],
            [['name', 'email', 'description', 'phone'], 'string'],
            [['visible', 'ok', 'user_ok', 'calc_sex', 'calc_servicetype', 'calc_lang', 'calc_eduform', 'calc_service', 'calc_office', 'calc_edulevel', 'calc_eduage', 'calc_class', 'calc_nomination', 'calc_way', 'user', 'flag_check', 'user_check', 'transform', 'user_transform', 'calc_studname', 'user_edit', 'user_visible'], 'integer'],
            [['data_ok', 'data', 'data_check', 'data_transform', 'data_edit', 'data_visible'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app','Full name'),
            'email' => 'E-mail',
            'description' => Yii::t('app','Notes'),
            'visible' => 'Visible',
            'ok' => 'Ok',
            'user_ok' => 'User Ok',
            'data_ok' => 'Data Ok',
            'phone' => Yii::t('app','Phone'),
            'calc_sex' => Yii::t('app','Sex'),
            'calc_servicetype' => Yii::t('app','Service type'),
            'calc_lang' => Yii::t('app','Language'),
            'calc_eduform' => Yii::t('app','Education type'),
            'calc_service' => Yii::t('app','Service'),
            'calc_office' => Yii::t('app','Office'),
            'calc_edulevel' => Yii::t('app','Level/Exam'),
            'calc_eduage' => Yii::t('app','Age'),
            'calc_class' => 'Calc Class',
            'calc_nomination' => 'Calc Nomination',
            'calc_way' => Yii::t('app','Way to Attract'),
            'user' => 'User',
            'data' => 'Data',
            'flag_check' => 'Flag Check',
            'user_check' => 'User Check',
            'data_check' => 'Data Check',
            'transform' => 'Transform',
            'user_transform' => 'User Transform',
            'data_transform' => 'Data Transform',
            'calc_studname' => Yii::t('app','Link to Client'),
            'user_edit' => 'User Edit',
            'data_edit' => 'Data Edit',
            'user_visible' => 'User Visible',
            'data_visible' => 'Data Visible',
        ];
    }
    
    /**
     * @deprecated
     * метод подменяет в строках идентификатор одного студента на идентификатор другого
     * @param integer $id1
     * @param integer $id2
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
