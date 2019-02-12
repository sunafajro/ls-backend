<?php

namespace app\models;

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
            'id' => 'ID',
            'calc_studname' => Yii::t('app', 'Student'),
            'number' => Yii::t('app', 'Contract number'),
            'date' => Yii::t('app', 'Contract date'),
            'signer' => Yii::t('app', 'Contract signer'),
        ];
    }

    public static function getClientContracts($sid)
    {
        $contracts = (new \yii\db\Query())
        ->select(['id' => 'id', 'number' => 'number', 'date' => 'date', 'signer' => 'signer'])
        ->from(['c' => static::tableName()])
        ->where([
            'calc_studname' => $sid
        ])
        ->orderBy(['date' => SORT_DESC])
        ->all();
        return $contracts;
    }
}
