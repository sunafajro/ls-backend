<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_edunorm".
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 * @property double $value
 * @property string $data
 */
class Edunorm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_edunorm';
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
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'visible' => Yii::t('app', 'Visible'),
            'value' => Yii::t('app', 'Value'),
            'data' => Yii::t('app', 'Data'),
        ];
    }

    public function getPaynorms()
    {
        $teachpaynorm = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name', 'value' => 'value'])
        ->from('calc_edunorm')
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
                ]
            ],
            'data' => $teachpaynorm
        ];
    }
}
