<?php

namespace app\models;

use app\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "student_contracts".
 *
 * @property integer $id
 * @property string  $student_id
 * @property string  $number
 * @property string  $date
 * @property string  $signer
 * @property integer $user_id
 * @property string  $created_at
 * @property integer $visible
 */
class Contract extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_contracts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['student_id', 'number', 'date', 'signer'], 'required'],
            [['number', 'signer'], 'string'],
            [['student_id', 'user_id', 'visible'], 'integer'],
            [['created_at', 'date'], 'safe'],
            [['visible'],    'default', 'value' => 1],
            [['user_id'],    'default', 'value' => Yii::$app->user->identity->id],
            [['created_at'], 'default', 'value' => date('Y-m-d')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'student_id' => Yii::t('app', 'Student'),
            'number'     => Yii::t('app', 'Contract number'),
            'date'       => Yii::t('app', 'Contract date'),
            'signer'     => Yii::t('app', 'Contract signer'),
            'user_id'    => Yii::t('app', 'User'),
            'created_at' => Yii::t('app', 'Created at'),
            'visible'    => Yii::t('app', 'Active'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;

        return $this->save(true, ['visible']);
    }

    public static function getClientContracts($sid = null)
    {
        $criteria = is_array($sid) ? ['in', 'student_id', $sid] : ['student_id' => $sid];
        $contracts = (new \yii\db\Query())
        ->select([
            'id'      => 'id',
            'number'  => 'number',
            'date'    => 'date',
            'signer'  => 'signer',
            'student' => 'student_id'
        ])
        ->from(['c' => static::tableName()])
        ->where($criteria)
        ->andWhere(['visible' => 1])
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
        ->update(self::tableName(), ['student_id' => $id1], ['student_id' => $id2])
        ->execute();

        return ($sql == 0) ? false : true;
    }
}
