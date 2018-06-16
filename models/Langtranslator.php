<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_langtranslator".
 *
 * @property integer $id
 * @property integer $calc_translator
 * @property integer $calc_translationlangs
 * @property integer $visible
 * @property string $data
 * @property integer $user
 */
class Langtranslator extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_langtranslator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_translator', 'calc_translationlangs', 'visible', 'data', 'user'], 'required'],
            [['calc_translator', 'calc_translationlangs', 'visible', 'user'], 'integer'],
            [['data'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'calc_translator' => Yii::t('app', 'Calc Translator'),
            'calc_translationlangs' => Yii::t('app', 'Language'),
            'visible' => Yii::t('app', 'Visible'),
            'data' => Yii::t('app', 'Data'),
            'user' => Yii::t('app', 'User'),
        ];
    }

    /**
     *  Метод получает список языков переводчика
     *  @return mixed
     */
    public static function getTranslatorLanguagesById($id)
    {
        $translator_langs = (new \yii\db\Query())
        ->select('lt.id as id, l.id as lid, l.name as lname, u.name as user, lt.data as date')
        ->from('calc_langtranslator lt')
        ->leftJoin('calc_translationlangs l', 'l.id=lt.calc_translationlangs')
        ->leftJoin('user u', 'u.id=lt.user')
        ->where('lt.visible=:vis and lt.calc_translator=:id', [':vis'=>1, ':id'=>$id])
        ->all();

        return $translator_langs;
    }
	
    /**
     *  Метод получает список языков переводчиков в виде многомерного массива
     */
    public static function getTranslatorLanguageList()
    {
        $languages = (new \yii\db\Query())
        ->select('lt.calc_translator as tid, l.id as lid, l.name as lname')
        ->from('calc_langtranslator lt')
        ->leftJoin('calc_translationlangs l', 'l.id=lt.calc_translationlangs')
        ->where('lt.visible=:vis', [':vis'=>1])
        ->orderby(['calc_translator'=>SORT_ASC,'name'=>SORT_ASC])
        ->all();
        
        return $languages;
    }
}
