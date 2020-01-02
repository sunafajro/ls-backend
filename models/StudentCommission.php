<?php

namespace app\models;

use app\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "student_grades".
 *
 * @property integer $id
 * @property integer $student_id
 * @property string  $date
 * @property float   $debt
 * @property float   $persent
 * @property float   $value
 * @property string  $comment
 * @property integer $visible
 * @property integer $user_id
 * @property integer $office_id
 * @property string  $created_at
 * 
 * @property Student $student
 * @property User    $user
 */

class StudentCommission extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;

    const COMMISSION_PERCENT = 0.2;

    /**
     * @inheritdoc
     */
    public static function tableName() : string
    {
        return 'student_commissions';
    }

    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            [['date', 'debt', 'percent', 'value', 'student_id', 'office_id', 'comment'], 'required'],
            [['comment'], 'string'],
            [['debt', 'percent', 'value'], 'number'],
            [['visible', 'user_id', 'student_id', 'office_id'], 'integer'],
            [['date', 'created_at'], 'safe'],
            [['visible'],    'default', 'value' => 1],
            [['user_id'],    'default', 'value' => Yii::$app->user->identity->id ?? 0],
            [['created_at'], 'default', 'value' => date('Y-m-d')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() : array
    {
        return [
            'id'         => '№',
            'student_id' => Yii::t('app', 'Student'),
            'visible'    => Yii::t('app', 'Visible'),
            'date'       => Yii::t('app', 'Commission date'),
            'user_id'    => Yii::t('app', 'Created by'),
            'office_id'  => Yii::t('app', 'Office'),
            'created_at' => Yii::t('app', 'Created date'),
            'debt'       => Yii::t('app', 'Debt'),
            'percent'    => Yii::t('app', 'Commission percent'),
            'value'      => Yii::t('app', 'Commission value'),
            'comment'    => Yii::t('app', 'Comment'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;

        return $this->save(true, ['visible']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function getStudentCommissionById(int $sid) : array
    {
        return (new \yii\db\Query())
            ->select([
                'id'      => 'c.id',
                'date'    => 'c.date',
                'debt'    => 'c.debt',
                'percent' => 'c.percent',
                'value'   => 'c.value',
                'user'    => 'u.name',
                'office'  => 'o.name',
                'comment' => 'c.comment',
            ])
            ->from(['c' => self::tableName()])
            ->innerJoin(['u' => User::tableName()], 'u.id = c.user_id')
            ->innerJoin(['o' => Office::tableName()], 'o.id = c.office_id')
            ->where([
                'c.student_id' => $sid,
                'c.visible' => 1,
            ])
            ->orderby(['c.id' => SORT_DESC])
            ->all();
    }
}