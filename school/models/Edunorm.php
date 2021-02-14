<?php

namespace school\models;

use Yii;

/**
 * This is the model class for table "calc_edunorm".
 *
 * @property integer $id
 * @property string  $name
 * @property integer $visible
 * @property double  $value
 * @property string  $data
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
            [['visible'], 'default', 'value' => 1],
            [['data'],    'default', 'value' => date("Y-m-d H:i:s")],
            [['name'],    'string'],
            [['visible'], 'integer'],
            [['value'],   'number'],
            [['data'],    'safe'],
            [['name', 'visible', 'value', 'data'], 'required'],
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
            'visible' => Yii::t('app', 'Visible'),
            'value'   => Yii::t('app', 'Value'),
            'data'    => Yii::t('app', 'Date'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    public static function getPaynorms()
    {
        $teachpaynorm = (new \yii\db\Query())
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
                ]
            ],
            'data' => $teachpaynorm
        ];
    }
}
