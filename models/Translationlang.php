<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_translationlangs".
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 * @property string $data
 * @property integer $user
 * @property integer $user_visible
 * @property string $data_visible
 */
class Translationlang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_translationlangs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'visible'], 'required'],
            [['visible', 'user', 'user_visible'], 'integer'],
            [['data', 'data_visible'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'data' => Yii::t('app', 'Data'),
            'user' => Yii::t('app', 'User'),
            'user_visible' => Yii::t('app', 'User Visible'),
            'data_visible' => Yii::t('app', 'Data Visible'),
        ];
    }
    
    /**
     *  Метод получает список языков и формирует из него одномерный массив
     */
    public static function getLanguageListSimple()
    {
        $languages = [];

        $languages_obj = self::find()
        ->select(['id'=>'id', 'name'=>'name'])
        ->where('visible=:one', [':one' => 1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        if($languages_obj !== NULL) {
            foreach($languages_obj as $l) {
                $languages[$l->id] = $l->name;
            }
        }
        return $languages;
    }
    
    /**
     *  Метод получает список языков перевода в виде многомерного массива объектов
     */
    public static function getLanguageList($params)
    {
        $languages = [];

        $languages_obj = self::find()
        ->select(['id'=>'id', 'name'=>'name'])
        ->where('visible=:one', [':one' => 1])
        ->andFilterWhere(['like', 'name', $params['TSS']])
        ->orderby(['name'=>SORT_ASC])
        ->all();
        
        if($languages_obj !== NULL) {
            $languages = $languages_obj;
        }

        return $languages;
    }
}
