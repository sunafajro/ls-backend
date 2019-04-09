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
            [['teacher_id', 'language_premium_id', 'user_id', 'created_at', 'visible', 'company'], 'required'],
            [['teacher_id', 'language_premium_id', 'user_id', 'visible', 'company'], 'integer'],
            [['created_at'], 'safe'],
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

    /* отдает список активных языковых надбавок преподавателя */
    public function getTeacherLanguagePremiums(int $tid)
    {
        $premiums = (new yii\db\Query())
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

        return $premiums;
    }

    public function getTeacherLanguagePremiumsForAccrual(int $tid)
    {
        $result = [];
        if (!empty($premiums = $this->getTeacherLanguagePremiums($tid))){
            /* готовим ставки преподавателей */
            $premid = [];
            $prem = [];
            foreach($premiums as $p) {
                if (!isset($premid[$p['lang_id']])) {
                    $premid[$p['lang_id']] = [
                        $p['company'] => $p['tlpid']
                    ];
                } else {
                    $premid[$p['lang_id']][$p['company']] = $p['tlpid'];
                }
                if (!isset($prem[$p['lang_id']])) {
                    $prem[$p['lang_id']] = [
                        $p['company'] => $p['value']
                    ];
                } else {
                    $prem[$p['lang_id']][$p['company']] = $p['value'];
                }
            }
            return [
                'premid' => $premid,
                'prem' => $prem
            ];
        } else {
            return $result;
        }
        
    }

    /* ищет у преподавателя другие активные надбавки по тому же языку и помечает их удаленными */
    public function removeDuplicateLanguagePremium($lpid, $company, $tid)
    {
        /* ищем язык по новой ставке */
        $lang = (new yii\db\Query())
        ->select(['id' => 'language_id'])
        ->from(LanguagePremium::tableName())
        ->where([
            'id'      => $lpid,
            'visible' => 1
        ])
        ->one();

        if (!empty($lang) && isset($lang['id'])) {
            /* ищем старую ставку */
            $old_premium = (new yii\db\Query())
            ->select([ 'id' => 'tlp.id'])
            ->from(['tlp' => static::tableName()])
            ->innerJoin(['lp' => LanguagePremium::tableName()], 'lp.id = tlp.language_premium_id')
            ->where([
                'lp.language_id' => $lang['id'],
                'tlp.visible'    => 1,
                'tlp.teacher_id' => $tid,
                'tlp.company'    => $company,
            ])
            ->one();

            if (!empty($old_premium) && isset($old_premium['id'])) {
                $premium = static::findOne($old_premium['id']);
                $premium->visible = 0;
                $premium->save();
            }
            return true;
        } else {
            return false;
        }
    }
}
