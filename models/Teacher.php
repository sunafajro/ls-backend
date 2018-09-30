<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_teacher".
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $address
 * @property integer $visible
 * @property double $value_corp
 * @property double $accrual
 * @property double $fund
 * @property string $email
 * @property string $social_link
 * @property integer $old
 * @property string $description
 * @property integer $calc_statusjob
 */
class Teacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_teacher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'calc_statusjob'], 'required'],
            [['name', 'phone', 'email', 'address', 'social_link', 'description'], 'string'],
            [['birthdate'],'date','format'=>'yyyy-mm-dd'],
            [['visible', 'old', 'calc_statusjob'], 'integer'],
            [['value_corp', 'accrual', 'fund'], 'number']
        ];
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['calc_teacher' => 'id']);
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Full name'),
            'birthdate'=>Yii::t('app','Birthdate'),
            'phone' => Yii::t('app', 'Phone'),
            'address' => Yii::t('app', 'Address'),
            'visible' => 'Visible',
            'value_corp' => Yii::t('app', 'Corp value'),
            'accrual' => 'Accrual',
            'fund' => 'Fund',
            'email' => Yii::t('app', 'Email'),
            'social_link' => Yii::t('app', 'Social'),
            'old' => Yii::t('app', 'Status'),
            'description' => Yii::t('app', 'Annotation'),
            'calc_statusjob' => Yii::t('app', 'Job type')
        ];
    }

    /* возвращает список преподавателей имеющих активные группы */
    public static function getTeachersWithActiveGroups($tid = null)
    {
        $teachers = (new \yii\db\Query())
        ->select('ctch.id as id, ctch.name as name')
        ->distinct()
        ->from('calc_teacher ctch')
        ->leftjoin('calc_teachergroup tg', 'tg.calc_teacher=ctch.id')
        ->leftJoin('calc_groupteacher cgt','cgt.id=tg.calc_groupteacher')
        ->where('ctch.visible=:vis and ctch.old=:old and cgt.visible=:vis', [':vis' => 1, ':old' => 0])
        ->andFilterWhere(['ctch.id' => $tid])
        ->orderBy(['ctch.name' => SORT_ASC])
        ->all();
        return $teachers;
    }

    /* Метод возвращает список действующих преподавателей в виде одномерного массива */
    public static function getTeachersInUserListSimple()
    {
        $teachers = [];

        $tmp_teachers = (new \yii\db\Query())
        ->select('ct.id as id, ct.name as name')
        ->from('calc_teacher ct')
        ->where('ct.visible=:vis and ct.old=:old', [':vis'=> 1,':old'=>0])
        ->orderBy(['ct.name'=>SORT_ASC])
        ->all();
        
        if(!empty($tmp_teachers)) {
            foreach($tmp_teachers as $t){
                $teachers[$t['id']] = $t['name'];
            }
        }

        return $teachers;
    }

    /* возвращает список активных групп преподавателя */
    public static function getActiveTeacherGroups($tid = null)
    {
        $groups = (new \yii\db\Query())
        ->select('gt.id as id, s.name as name')
        ->from('calc_teachergroup tg')
        ->innerJoin('calc_groupteacher gt', 'tg.calc_groupteacher=gt.id')
        ->innerJoin('calc_service s','s.id=gt.calc_service')
        ->where('gt.visible=:vis and tg.calc_teacher=:tid', [':vis' => 1, ':tid' => $tid])
        ->orderBy(['s.name' => SORT_ASC])
        ->all();
        return $groups;
    }
}
