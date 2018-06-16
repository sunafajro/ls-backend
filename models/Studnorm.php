<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_studnorm".
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 * @property double $value
 * @property string $data
 */
class Studnorm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_studnorm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'visible', 'value', 'data'], 'required'],
            [['name'], 'string'],
            [['visible'], 'integer'],
            [['value'], 'number'],
            [['data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Name'),
            'visible' => 'Visible',
            'value' => Yii::t('app', 'Value'),
            'data' => 'Data',
        ];
    }

    public static function getPaynorms()
    {
        $studpaynorm = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name', 'value' => 'value'])
        ->from('calc_studnorm')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        return [
            'columns' => [
                [
                    'id'   => 'id',
                    'name' => '№',
                    'show' => true
                ],
                [
                    'id'   => 'name',
                    'name' => Yii::t('app', 'Name'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Value'),
                    'show' => true
                ],
            ],
            'data'    => $studpaynorm
        ];
    }
}
