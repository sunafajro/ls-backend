<?php

namespace school\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "calc_coefficients".
 *
 * @property integer $id
 * @property integer $studcount
 * @property float   $value
 * @property integer $visible
 */

class Coefficient extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_coefficient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['value'], 'number'],
            [['studcount', 'visible'], 'integer'],
            [['studcount', 'value', 'visible'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'studcount' => Yii::t('app', 'Students count'),
            'value'     => Yii::t('app', 'Value'),
            'visible'   => Yii::t('app', 'Visible'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }
    
    /**
     * Метод возвращает список доступных коэфициентов в виде многомерного массива.
     * @return mixed
     */
    public static function getCoefficientsList()
    {
        $coefficients = (new \yii\db\Query())
        ->select([
            'id'        => 'id',
            'studcount' => 'studcount',
            'value'     => 'value'
        ])
        ->from(self::tableName())
        ->where(['visible' => 1])
        ->orderby(['studcount' => SORT_ASC])
        ->all();

        return [
            'columns' => [
                [
                    'id'   => 'id',
                    'name' => '№',
                    'show' => true
                ],
                [
                    'id'   => 'studcount',
                    'name' => Yii::t('app', 'Student count'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Value'),
                    'show' => true
                ],
            ],
            'data'    => $coefficients
        ];
    }
}