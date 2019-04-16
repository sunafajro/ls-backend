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
    const EXAM_YLE_STARTERS = 'yleStarters';
    const EXAM_YLE_MOVERS   = 'yleMovers';
    const EXAM_YLE_FLYERS   = 'yleFlyers';
    const EXAM_KET_A2       = 'ketA2';
    const EXAM_PET_B1       = 'petB1';
    const EXAM_FCE_B2       = 'fceB2';

    const EXAM_CONTENT_LISTENING           = 'listening';
    const EXAM_CONTENT_READING_AND_WRITING = 'readingAndWriting';
    const EXAM_CONTENT_SPEAKING            = 'speaking';
    const EXAM_CONTENT_READING             = 'reading';
    const EXAM_CONTENT_USE_OF_ENGLISH      = 'useOfEnglish';
    const EXAM_CONTENT_WRITING             = 'writing';

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

    public static function getExams() : array
    {
        return [
            self::EXAM_YLE_STARTERS => 'YLE starters',
            self::EXAM_YLE_MOVERS   => 'YLE movers',
            self::EXAM_YLE_FLYERS   => 'YLE flyers',
            self::EXAM_KET_A2       => 'KET - A2',
            self::EXAM_PET_B1       => 'PET - B1',
            self::EXAM_FCE_B2       => 'FCE - B2',
        ];
    }

    public static function getExamContentTypes() : array
    {
        return [
            self::EXAM_CONTENT_LISTENING            => 'Listening',
            self::EXAM_CONTENT_READING_AND_WRITING  => 'Reading & Writing',
            self::EXAM_CONTENT_SPEAKING             => 'Speaking',
            self::EXAM_CONTENT_READING              => 'Reading',
            self::EXAM_CONTENT_USE_OF_ENGLISH       => 'Use of English',
            self::EXAM_CONTENT_WRITING              => 'Writing',
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

    public function getExamContents($exam)
    {
        if ($exam === StudentGrade::EXAM_YLE_STARTERS ||
            $exam === StudentGrade::EXAM_YLE_MOVERS ||
            $exam === StudentGrade::EXAM_YLE_FLYERS) {
            return [
                'show' => '.js--exam-contents-first',
                'hide' => '.js--exam-contents-second',
            ];
        } else if ($exam === StudentGrade::EXAM_KET_A2 ||
                   $exam === StudentGrade::EXAM_PET_B1 ||
                   $exam === StudentGrade::EXAM_FCE_B2) {
            return [
                'hide' => '.js--exam-contents-first',
                'show' => '.js--exam-contents-second',
            ];
        } else {
            return '';
        }
    }
}