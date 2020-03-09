<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_timenorm".
 *
 * @property integer $id
 * @property string  $name
 * @property double  $value
 * @property integer $visible
 * @property string  $data
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
            [['visible'], 'default', 'value' => 1],
            [['data'],    'default', 'value' => date("Y-m-d H:i:s")],
            [['name'],    'string'],
            [['value'],   'number'],
            [['visible'], 'integer'],
            [['data'],    'safe'],
            [['name', 'value', 'visible', 'data'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => Yii::t('app', 'ID'),
            'name'    => Yii::t('app', 'Name'),
            'value'   => Yii::t('app', 'Value'),
            'visible' => Yii::t('app', 'Visible'),
            'data'    => Yii::t('app', 'Data'),
        ];
    }

    public static function getTimenorms()
    {
        $timenorm = (new \yii\db\Query())
        ->select([
            'id'    => 'id',
            'name'  => 'name',
            'value' => 'value'
        ])
        ->from(self::tableName())
        ->where(['visible' => 1])
        ->orderby(['name' => SORT_ASC])
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
