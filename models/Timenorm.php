<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_timenorm".
 *
 * @property integer $id
 * @property string $name
 * @property double $value
 * @property integer $visible
 * @property string $data
 */
class Timenorm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_timenorm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value', 'visible', 'data'], 'required'],
            [['name'], 'string'],
            [['value'], 'number'],
            [['visible'], 'integer'],
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
            'value' => Yii::t('app', 'Value'),
            'visible' => Yii::t('app', 'Visible'),
            'data' => Yii::t('app', 'Data'),
        ];
    }

    public function getTimenorms()
    {
        $timenorm = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name', 'value' => 'value'])
        ->from('calc_timenorm')
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
            'data'    => $timenorm
        ];
    }
}
