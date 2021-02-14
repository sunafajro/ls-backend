<?php

namespace school\models;

use Yii;

/**
 * This is the model class for table "calc_translation".
 *
 * @property integer $id
 * @property string $data
 * @property integer $user
 * @property integer $user_end
 * @property string $data_end
 * @property integer $calc_translationclient
 * @property integer $calc_translator
 * @property integer $from_language
 * @property integer $to_language
 * @property string $nomination
 * @property integer $calc_translationnorm
 * @property double $printsymbcount
 * @property double $accunitcount
 * @property double $value
 * @property string $description
 * @property string $receipt
 * @property integer $visible
 * @property integer $user_visible
 * @property string $data_visible
 */
class Translation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data', 'user', 'calc_translationclient', 'calc_translator', 'from_language', 'to_language', 'nomination', 'calc_translationnorm', 'printsymbcount', 'accunitcount', 'value', 'value_correction', 'visible'], 'required'],
            [['data', 'data_end', 'data_visible'], 'safe'],
            [['user', 'user_end', 'calc_translationclient', 'calc_translator', 'from_language', 'to_language', 'calc_translationnorm', 'visible', 'user_visible'], 'integer'],
            [['printsymbcount', 'accunitcount', 'value_correction', 'value'], 'number'],
            [['description'], 'string'],
            [['nomination', 'receipt'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'data' => Yii::t('app', 'Add date'),
            'user' => Yii::t('app', 'User'),
            'user_end' => Yii::t('app', 'User End'),
            'data_end' => Yii::t('app', 'End date'),
            'calc_translationclient' => Yii::t('app', 'Client'),
            'calc_translator' => Yii::t('app', 'Translator'),
            'from_language' => Yii::t('app', 'From language'),
            'to_language' => Yii::t('app', 'To language'),
            'nomination' => Yii::t('app', 'Nomination'),
            'calc_translationnorm' => Yii::t('app', 'Cost for one account unit'),
            'printsymbcount' => Yii::t('app', 'Print symbols count'),
            'accunitcount' => Yii::t('app', 'Account unit count'),
            'value' => Yii::t('app', 'Sum'),
            'value_correction' => Yii::t('app', 'Sum correction'),
            'description' => Yii::t('app', 'Description'),
            'receipt' => Yii::t('app', 'Receipt'),
            'visible' => Yii::t('app', 'Visible'),
            'user_visible' => Yii::t('app', 'User Visible'),
            'data_visible' => Yii::t('app', 'Data Visible'),
        ];
    }
    
    /**
     *  Метод получает список переводов и формирует из него одномерный массив
     */
    public static function getTranslationList($params)
    {
        $translations = (new \yii\db\Query())
        ->select('t.id as tid, t.data as tdate, t.data_end as tenddate, tc.name as client, tr.name as translator, fl.name as from_lang, tl.name as to_lang, t.nomination as nomination, tn.value as tnorm, t.printsymbcount as pscount, t.accunitcount as aucount, t.value as value, t.description as desc, t.receipt as receipt')
        ->from('calc_translation t')
        ->leftJoin('calc_translator tr', 'tr.id=t.calc_translator')
        ->leftJoin('calc_translationclient tc', 'tc.id=t.calc_translationclient')
        ->leftJoin('calc_translationlangs fl', 'fl.id=t.from_language')
        ->leftJoin('calc_translationlangs tl', 'tl.id=t.to_language')
        ->leftJoin('calc_translationnorm tn', 'tn.id=t.calc_translationnorm')
        ->where('t.visible=:vis', [':vis' => 1])
        ->andFilterWhere(['or', ['t.from_language'=>$params['LANG']], ['t.to_language'=>$params['LANG']]])
        ->andFilterWhere(['like', 't.nomination', $params['TSS']])
        ->andFilterWhere(['MONTH(t.data)'=>$params['MONTH']])
        ->andFilterWhere(['YEAR(t.data)'=>$params['YEAR']])
        ->orderby(['t.data'=>SORT_DESC, 't.data_end'=>SORT_DESC])
        ->all();
        
        return $translations;
    }
}
