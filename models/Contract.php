<?php

namespace app\models;

use app\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "calc_student_contract".
 *
 * @property integer $id
 * @property string $calc_studname
 * @property string $number
 * @property string $date
 * @property string $signer
 */
class Contract extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_student_contract';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_studname', 'number', 'date', 'signer'], 'required'],
            [['number', 'signer'], 'string'],
            [['calc_studname'], 'integer'],
            [['date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'calc_studname' => Yii::t('app', 'Student'),
            'number'        => Yii::t('app', 'Contract number'),
            'date'          => Yii::t('app', 'Contract date'),
            'signer'        => Yii::t('app', 'Contract signer'),
        ];
    }

    public static function getClientContracts($sid = null)
    {
        $criteria = is_array($sid) ? ['in', 'calc_studname', $sid] : ['calc_studname' => $sid];
        $contracts = (new \yii\db\Query())
        ->select([
            'id'      => 'id',
            'number'  => 'number',
            'date'    => 'date',
            'signer'  => 'signer',
            'student' => 'calc_studname'
        ])
        ->from(['c' => static::tableName()])
        ->where($criteria)
        ->orderBy(['date' => SORT_DESC])
        ->all();
        return $contracts;
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
