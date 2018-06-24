<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_teacherlangpremium".
 *
 * @property integer $id
 * @property integer $calc_teacher
 * @property integer $calc_langpremium
 * @property integer $user
 * @property string $created_at
 * @property integer $visible
 */
class Teacherlangpremium extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_teacherlangpremium';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_teacher', 'calc_langpremium', 'user', 'created_at', 'visible'], 'required'],
            [['calc_teacher', 'calc_langpremium', 'user', 'visible'], 'integer'],
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
            'calc_teacher' => Yii::t('app', 'Teacher'),
            'calc_langpremium' => Yii::t('app', 'Language premium'),
            'user' => Yii::t('app', 'User'),
            'created_at' => Yii::t('app', 'Created At'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }

    /* отдает список активных языковых надбавок преподавателя */
    public static function getTeacherLangPremiums($tid)
    {
        $premiums = (new yii\db\Query())
        ->select('tlp.id as tlpid, lp.id as lpid, l.id as lang_id, l.name as language, lp.value as value, lp.visible as visible, tlp.created_at as created_at')
        ->from('calc_teacherlangpremium tlp')
        ->innerJoin('calc_langpremium lp', 'lp.id=tlp.calc_langpremium')
        ->innerJoin('calc_lang l', 'lp.calc_lang=l.id')
        ->where('tlp.visible=:one AND lp.visible=:one AND l.visible=:one AND tlp.calc_teacher=:tid', 
            [':one' => 1, ':tid' => $tid])
        ->orderby(['l.name' => SORT_ASC, 'lp.value' => SORT_ASC])
        ->all();

        return $premiums;
    }

    public static function getTeacherLangPremiumsForAccrual($tid)
    {
        $result = [];
        if (!empty($premiums = self::getTeacherLangPremiums($tid))){
            /* готовим ставки преподавателей */
            $premid = [];
            $prem = [];
            foreach($premiums as $p) {
                /* создаем массив надбавок */
                $premid[$p['lang_id']] = $p['tlpid'];
                $prem[$p['lang_id']] = $p['value'];
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
    public static function removeDuplicateLangPremium($lpid, $tid)
    {
        /* ищем язык по новой ставке */
        $lang = (new yii\db\Query())
        ->select('calc_lang as id')
        ->from('calc_langpremium')
        ->where('id=:lpid AND visible=:one', [':lpid' => $lpid, ':one' => 1])
        ->one();

        if (!empty($lang) && isset($lang['id'])) {
            /* ищем старую ставку */
            $old_premium = (new yii\db\Query())
            ->select('tlp.id as id')
            ->from('calc_teacherlangpremium tlp')
            ->innerJoin('calc_langpremium lp', 'lp.id=tlp.calc_langpremium')
            ->where('lp.calc_lang=:lang AND tlp.visible=:one AND tlp.calc_teacher=:tid',
            [':lang' => $lang['id'], ':one' => 1, ':tid' => $tid])
            ->one();

            if (!empty($old_premium) && isset($old_premium['id'])) {
                /* помечаем старую ставку удаленной */
                $update = (new yii\db\Query())
                ->createCommand()
                ->update('calc_teacherlangpremium', ['visible' => 0], ['id' => $old_premium['id']])
                ->execute();
            }

            return true;

        } else {

            return false;
        }
    }
}
