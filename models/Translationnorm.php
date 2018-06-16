<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_translationnorm".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $visible
 * @property double $value
 * @property string $data_end
 * @property integer $user
 * @property string $data
 * @property integer $user_visible
 * @property string $data_visible
 */
class Translationnorm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_translationnorm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'visible', 'value', 'user', 'data'], 'required'],
            [['type', 'visible', 'user', 'user_visible'], 'integer'],
            [['value'], 'number'],
            [['data', 'data_visible'], 'safe'],
            [['name'], 'string', 'max' => 255]
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
            'type' => Yii::t('app', 'Type'),
            'visible' => Yii::t('app', 'Visible'),
            'value' => Yii::t('app', 'Value'),
            'user' => Yii::t('app', 'User'),
            'data' => Yii::t('app', 'Data'),
            'user_visible' => Yii::t('app', 'User Visible'),
            'data_visible' => Yii::t('app', 'Data Visible'),
        ];
    }

    /**
     *  Метод получает список норм оплаты и формирует из него одномерный массив
     */
    public static function getNormListSimple()
    {
        $norms = [];
        $norms_obj = self::find()->select(['id'=>'id', 'name'=>'name', 'type' => 'type'])->where('visible=:one', [':one' => 1])->orderby(['name'=>SORT_ASC])->all();
        if($norms_obj !== NULL) {
            foreach($norms_obj as $c) {
                $norms[$c->id] = $c->name . ($c->type==1 ? ' (письм)' : ' (устн)');
            }
        }
        return $norms;
    }

    /**
     *  Метод получает список норм оплаты в виде массива объектов
     */
    public static function getNormList($params)
    {
        $norms = [];

        $norms_obj = self::find()->
        select(['id'=>'id', 'name'=>'name', 'type' => 'type', 'value' => 'value'])
        ->where('visible=:one', [':one' => 1])
        ->andFilterWhere(['like', 'name', $params['TSS']])
        ->orderby(['type'=>SORT_ASC, 'name'=>SORT_ASC])->all();
        if($norms_obj !== NULL) {
            $norms = $norms_obj;
        }

        return $norms;
    }
}
