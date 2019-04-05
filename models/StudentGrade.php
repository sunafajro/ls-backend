<?php

namespace app\models;

use Yii;

use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "student_grades".
 *
 * @property integer $id
 * @property string $visible
 * @property string $date
 * @property string $user
 * @property string $score
 * @property string $type
 * @property string $description
 * @property string $calc_studname
 */

class StudentGrade extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_grades';
    }
 
    public static function getGradeTypes()
    {
        return [
            0 => 'Баллы',
            1 => 'Проценты',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user', 'calc_studname',], 'required'],
            [['description'], 'string'],
            [['visible', 'user', 'calc_studname', 'score', 'type'], 'integer'],
            [['date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'visible' => Yii::t('app', 'Status'),
            'date' => Yii::t('app', 'Grade date'),
            'user' => Yii::t('app', 'Added by'),
            'score' => Yii::t('app', 'Score'),
            'type' => Yii::t('app', 'Type'),
            'description' => Yii::t('app', 'Description'),
            'calc_studname' => Yii::t('app', 'Student'),
        ];
    }

    /**
     *  метод возвращает список оценок студента
     */
    public function getStudentGrades(string $sid)
    {
        $query = (new \yii\db\Query())
        ->select([
            'id' => 'sg.id',
            'date' => 'sg.date',
            'userName' => 'u.name',
            'score' => 'sg.score',
            'type' => 'sg.type',
            'description' => 'sg.description',
            'studentId' => 'sg.calc_studname',
            'studentName' => 's.name',
        ])
        ->from(['sg' => static::tableName()])
        ->innerJoin(['u' => 'user'], 'sg.user = u.id')
        ->innerJoin(['s' => 'calc_studname'], 'sg.calc_studname = s.id')
        ->where([
            'sg.calc_studname' => $sid,
            'sg.visible' => 1,
        ]);
        
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [
                    'date',
                ],
                'defaultOrder' => [
                    'date' => SORT_DESC
                ],
            ],
        ]);
    }
}