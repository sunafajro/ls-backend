<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

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
class Langtranslator extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'calc_langtranslator';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
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
    public function attributeLabels(): array
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
     *  @return array
     */
    public static function getTranslatorLanguagesById($id): array
    {
        return (new \yii\db\Query())
            ->select('lt.id as id, l.id as lid, l.name as lname, u.name as user, lt.data as date')
            ->from(['lt' => self::tableName()])
            ->leftJoin(['l' => Translationlang::tableName()], 'l.id = lt.calc_translationlangs')
            ->leftJoin(['u' => BaseUser::tableName()], 'u.id = lt.user')
            ->where([
                'lt.visible' => 1,
                'lt.calc_translator' => $id,
            ])
            ->all();
    }
	
    /**
     *  Метод получает список языков переводчиков в виде многомерного массива
     *  @return array
     */
    public static function getTranslatorLanguageList(): array
    {
        return (new \yii\db\Query())
            ->select('lt.calc_translator as tid, l.id as lid, l.name as lname')
            ->from(['lt' => self::tableName()])
            ->leftJoin(['l' => Translationlang::tableName()], 'l.id = lt.calc_translationlangs')
            ->where(['lt.visible' => 1])
            ->orderby(['lt.calc_translator' => SORT_ASC, 'l.name' => SORT_ASC])
            ->all();
    }
}
