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
 * @property integer $type
 * @property string $contents
 * @property string $description
 * @property string $calc_studname
 */

class StudentGrade extends \yii\db\ActiveRecord
{
    private $examTypes = [
       'yleStarters' => 'YLE starters',
       'yleMovers' => 'YLE movers',
       'yleFlyers' => 'YLE flyers',
       'ketA2' => 'KET - A2',
       'petB1' => 'PET - B2',
       'fceB2' => 'FCE - B2',
    ];

    private $examContentTypes = [
        'listening' => 'Listening',
        'readingAndWriting' => 'Reading & Writing',
        'speaking' => 'Speaking',
        'reading' => 'Reading',
        'useOfEnglish' => 'Use of English',
        'writing' => 'Writing',
     ];

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
            [['date', 'description', 'score', 'user', 'calc_studname'], 'required'],
            [['description', 'score'], 'string'],
            [['visible', 'user', 'calc_studname', 'type'], 'integer'],
            [['date', 'contents'], 'safe']
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
            'type' => Yii::t('app', 'Score type'),
            'contents' => Yii::t('app', 'Exam contents'),
            'description' => Yii::t('app', 'Exam description'),
            'calc_studname' => Yii::t('app', 'Student'),
        ];
    }

    /**
     *  метод возвращает одну оценку по id
     */
    public function getAttestation($id)
    {
        $attestation = (new \yii\db\Query())
        ->select([
            'id' => 'sg.id',
            'date' => 'sg.date',
            'score' => 'sg.score',
            'type' => 'sg.type',
            'description' => 'sg.description',
            'contents' => 'sg.contents',
            'studentId' => 'sg.calc_studname',
            'studentName' => 's.name',
        ])
        ->from(['sg' => 'student_grades'])
        ->innerJoin(['s' => 'calc_studname'], 'sg.calc_studname = s.id')
        ->where([
            'sg.id' => $id,
            'sg.visible' => 1,
        ])
        ->one();

        return $attestation;
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
            'contents' => 'sg.contents',
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

    public function getExams()
    {
        return $this->examTypes;
    }

    public function getExamContentTypes()
    {
        return $this->examContentTypes;
    }

    public function getExamContents($exam)
    {
        if ($exam === 'yleStarters' || $exam === 'yleMovers' || $exam === 'yleFlyers') {
            return [
                'show' => '.js--exam-contents-first',
                'hide' => '.js--exam-contents-second',
            ];
        } else if ($exam === 'ketA2' || $exam === 'petB1' || $exam === 'fceB2') {
            return [
                'hide' => '.js--exam-contents-first',
                'show' => '.js--exam-contents-second',
            ];
        } else {
            return '';
        }
    }
}