<?php

namespace app\models;

use Yii;
use app\models\Lang;
use app\models\LanguagePremium;
/**
 * This is the model class for table "teacher_language_premiums".
 *
 * @property integer $id
 * @property integer $teacher_id
 * @property integer $language_premium_id
 * @property integer $user_id
 * @property string $created_at
 * @property integer $visible
 * @property integer $company
 */
class TeacherLanguagePremium extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teacher_language_premiums';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible'],    'default', 'value' => 1],
            [['created_at'], 'default', 'value' => date('Y-m-d')],
            [['user_id'],    'default', 'value' => Yii::$app->session->get('user.uid')],
            [['teacher_id', 'language_premium_id', 'user_id', 'visible', 'company'], 'integer'],
            [['created_at'], 'safe'],
            [['teacher_id', 'language_premium_id', 'user_id', 'created_at', 'visible', 'company'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'teacher_id' => Yii::t('app', 'Teacher'),
            'language_premium_id' => Yii::t('app', 'Language premium'),
            'user_id' => Yii::t('app', 'User'),
            'created_at' => Yii::t('app', 'Created At'),
            'visible' => Yii::t('app', 'Visible'),
            'company' => Yii::t('app', 'Job place'),
        ];
    }

    /**
     * Список активных языковых надбавок преподавателя
     * @param int $tid
     *
     * @return array
     */
    public static function getTeacherLanguagePremiums(int $tid) : array
    {
        return (new yii\db\Query())
        ->select([
            'tlpid'      => 'tlp.id',
            'lpid'       => 'lp.id',
            'lang_id'    => 'l.id',
            'language'   => 'l.name',
            'value'      => 'lp.value',
            'visible'    => 'lp.visible',
            'created_at' => 'tlp.created_at',
            'company'    => 'tlp.company',
        ])
        ->from(['tlp' => static::tableName()])
        ->innerJoin(['lp' => LanguagePremium::tableName()], 'lp.id = tlp.language_premium_id')
        ->innerJoin(['l' => Lang::tableName()], 'lp.language_id = l.id')
        ->where([
            'tlp.visible' => 1,
            'lp.visible' => 1,
            'l.visible' => 1,
            'tlp.teacher_id' => $tid
        ])
        ->orderby(['l.name' => SORT_ASC, 'lp.value' => SORT_ASC])
        ->all();
    }

    /**
     * Массив активных языковых надбавок преподавателя
     * @param int $tid
     *
     * @return array|array[]
     */
    public static function getTeacherLanguagePremiumsForAccrual(int $tid) : array
    {
        $result = [];

        if (!empty($premiums = self::getTeacherLanguagePremiums($tid))){
            foreach($premiums as $premium) {
                if (!isset($result[$premium['lang_id']])) {
                    $result[$premium['lang_id']] = [];
                }
                $result[$premium['lang_id']][$premium['company']] = [
                    'id'    => $premium['tlpid'],
                    'value' => $premium['value'],
                ];
            }
        }

        return $result;
    }

    /**
     * Поиск и удаление у преподавателя предыдущих активных надбавок с таким же языком как и у новой надбавки
     * @param integer $lpId
     * @param integer $company
     * @param integer $tid
     *
     * @return bool
     */
    public static function removeDuplicateLanguagePremium(int $lpId, int $company, int $tid) : bool
    {
        /* ищем язык по новой ставке */
        $lang = (new yii\db\Query())
        ->select(['id' => 'language_id'])
        ->from(LanguagePremium::tableName())
        ->where([
            'id'      => $lpId,
            'visible' => 1
        ])
        ->one();

        if (!empty($lang) && isset($lang['id'])) {
            /* ищем старую ставку */
            $oldPremium = (new yii\db\Query())
                ->select([ 'id' => 'tlp.id'])
                ->from(['tlp' => self::tableName()])
                ->innerJoin(['lp' => LanguagePremium::tableName()], 'lp.id = tlp.language_premium_id')
                ->where([
                    'lp.language_id' => $lang['id'],
                    'tlp.visible'    => 1,
                    'tlp.teacher_id' => $tid,
                    'tlp.company'    => $company,
                ])
                ->one();

            if (!empty($oldPremium) && isset($oldPremium['id'])) {
                $premium = self::findOne($oldPremium['id']);
                $premium->visible = 0;
                return $premium->save();
            }

            return true;
        }

        return false;
    }
}
