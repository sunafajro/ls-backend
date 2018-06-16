<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_lang".
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 */
class Lang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'visible'], 'required'],
            [['name'], 'string'],
            [['visible'], 'integer']
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
        ];
    }

    /* возвращает список языков в виде ассоциативного массива */
    public static function getLanguages()
    {
        $languages = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_lang')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        return $languages;
    }

    /* возвращает список языков в виде одномерного массива */
    public static function getLanguagesSimple()
    {
        $tmp_languages = self::getLanguages();
        
        $languages = [];
        foreach($tmp_languages as $l) {
            if ((int)$l['id'] !== 16) {
                $languages[$l['id']] = $l['name'];
            }
        }

        return $languages;
    }
}
