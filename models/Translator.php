<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_translator".
 *
 * @property integer $id
 * @property string $name
 * @property string $lname
 * @property string $fname
 * @property string $mname
 * @property string $phone
 * @property string $email
 * @property string $skype
 * @property integer $user
 * @property string $data
 * @property integer $visible
 * @property integer $notarial
 * @property string $url
 * @property string $description
 */
class Translator extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_translator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'lname', 'fname', 'user', 'data', 'visible'], 'required'],
            [['user', 'visible', 'notarial'], 'integer'],
            [['data'], 'safe'],
            [['description'], 'string'],
            [['name', 'lname', 'fname', 'mname', 'phone', 'email', 'skype', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Full name'),
            'fname' => Yii::t('app', 'First name'),
            'lname' => Yii::t('app', 'Last name'),
            'mname' => Yii::t('app', 'Middle name'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'skype' => Yii::t('app', 'Skype'),
            'user' => Yii::t('app', 'User'),
            'data' => Yii::t('app', 'Data'),
            'visible' => Yii::t('app', 'Visible'),
            'notarial' => Yii::t('app', 'Notarial'),
            'url' => Yii::t('app', 'Site'),
            'description' => Yii::t('app', 'Description'),
        ];
    }
    
    /**
     *  Метод получает список переводчиков и формирует из него одномерный массив
     */
    public static function getTranslatorListSimple()
    {
        $translators = [];
        $translators_obj = self::find()->select(['id'=>'id', 'name'=>'name'])->where('visible=:one', [':one' => 1])->orderby(['name'=>SORT_ASC])->all();
        if($translators_obj !== NULL) {
            foreach($translators_obj as $c) {
                $translators[$c->id] = $c->name;
            }
        }
        return $translators;
    }

    /**
     *  Метод получает список переводов и формирует из него одномерный массив
     */
    public static function getTranslatorList($params)
    {
        $translators = (new \yii\db\Query())
        ->select('t.id as id, t.name as name, t.phone as phone, t.email as email, t.notarial as notarial, t.url as url, t.skype as skype, t.description as description')
        ->from('calc_translator t');
        if($params['LANG']){
            $translators = $translators->leftJoin('calc_langtranslator lt', 'lt.calc_translator=t.id');
        }
        $translators = $translators->where('t.visible=:vis', [':vis'=>1])
        ->andFilterWhere(['t.notarial'=>$params['NOTAR']])
        ->andFilterWhere(['like', 't.name', $params['TSS']]);
        if($params['LANG']){
            $translators = $translators->andFilterWhere(['lt.calc_translationlangs'=>$params['LANG']]);
        }
        $translators = $translators->orderby(['t.name'=>SORT_ASC])
        ->all();
        
        return $translators;
    }
}
