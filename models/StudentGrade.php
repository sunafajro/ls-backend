<?php

namespace app\models;

use app\traits\StudentMergeTrait;
use kartik\mpdf\Pdf;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "student_grades".
 *
 * @property integer $id
 * @property integer $visible
 * @property string  $date
 * @property integer $user
 * @property string  $score
 * @property integer $type
 * @property string  $contents
 * @property string  $description
 * @property integer $calc_studname
 */

class StudentGrade extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;

    const EXAM_TYPE_DEFAULT     = 0;
    
    const EXAM_YLE_STARTERS    = 'yleStarters';
    const EXAM_YLE_MOVERS      = 'yleMovers';
    const EXAM_YLE_FLYERS      = 'yleFlyers';
    const EXAM_KET_A2          = 'ketA2';
    const EXAM_PET_B1          = 'petB1';
    const EXAM_FCE_B2          = 'fceB2';
    const EXAM_TEXT_BOOK_FINAL = 'text_book_final';
    const EXAM_OLYMPIAD        = 'olympiad';
    const EXAM_DICTATION       = 'dictation';

    const EXAM_CONTENT_LISTENING           = 'listening';
    const EXAM_CONTENT_READING_AND_WRITING = 'readingAndWriting';
    const EXAM_CONTENT_SPEAKING            = 'speaking';
    const EXAM_CONTENT_READING             = 'reading';
    const EXAM_CONTENT_USE_OF_ENGLISH      = 'useOfEnglish';
    const EXAM_CONTENT_WRITING             = 'writing';
    
    const EXAM_CONTENT_WROTE_AN           = 'wroteAn';
    const EXAM_CONTENT_TOOK_PART_IN       = 'tookPartIn';
    const EXAM_CONTENT_BECAME_WHO         = 'becameWho';
    const EXAM_CONTENT_TOOK_THE_COURSE    = 'tookTheCourse';
    const EXAM_CONTENT_ACCORDING_TO_BOOK  = 'according_to_book';
    const EXAM_CONTENT_COURSE_HOURS_COUNT = 'course_hours_count';

    /**
     * @inheritdoc
     */
    public static function tableName() : string
    {
        return 'student_grades';
    }

    /**
     * @deprecated
     * 
     * @return array
     */
    public static function getGradeTypes() : array
    {
        return [
            0 => 'Баллы',
            1 => 'Проценты',
        ];
    }

    /**
     * Возвращает список типов экзаменов
     * @return array
     */
    public static function getExams() : array
    {
        return [
            self::EXAM_YLE_STARTERS    => 'YLE starters',
            self::EXAM_YLE_MOVERS      => 'YLE movers',
            self::EXAM_YLE_FLYERS      => 'YLE flyers',
            self::EXAM_KET_A2          => 'KET - A2',
            self::EXAM_PET_B1          => 'PET - B1',
            self::EXAM_FCE_B2          => 'FCE - B2',
            self::EXAM_TEXT_BOOK_FINAL => 'Итоговый тест по учебнику',
            self::EXAM_OLYMPIAD        => 'Олимпиада',
            self::EXAM_DICTATION       => 'Тотальный диктант',
        ];
    }

    /**
     * Возвращает название поля из содержимого экзамена
     * @param string $type
     * 
     * @return string
     */
    public static function getExamContentType(string $type) : string
    {
        $exams = self::getExamContentTypes();
        return $exams[$type] ?? '';
    }

    /**
     * Возвращает список полей с содержимым экзамена
     * 
     * @return array
     */
    public static function getExamContentTypes() : array
    {
        return [
            self::EXAM_CONTENT_LISTENING            => 'Listening',
            self::EXAM_CONTENT_READING_AND_WRITING  => 'Reading & Writing',
            self::EXAM_CONTENT_SPEAKING             => 'Speaking',
            self::EXAM_CONTENT_READING              => 'Reading',
            self::EXAM_CONTENT_USE_OF_ENGLISH       => 'Use of English',
            self::EXAM_CONTENT_WRITING              => 'Writing',
            self::EXAM_CONTENT_WROTE_AN             => 'Написал',
            self::EXAM_CONTENT_TOOK_PART_IN         => 'Принял участие в',
            self::EXAM_CONTENT_BECAME_WHO           => 'Стал',
            self::EXAM_CONTENT_TOOK_THE_COURSE      => 'По прохождению курса',
            self::EXAM_CONTENT_ACCORDING_TO_BOOK    => 'По учебнику',
            self::EXAM_CONTENT_COURSE_HOURS_COUNT   => 'В количестве часов',

        ];
    }
    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            [['date', 'description', 'calc_studname'], 'required'],
            [['description', 'score'], 'string'],
            [['visible', 'user', 'calc_studname', 'type'], 'integer'],
            [['user'],    'default', 'value' => Yii::$app->user->identity->id ?? 0],
            [['type'],    'default', 'value' => self::EXAM_TYPE_DEFAULT],
            [['visible'], 'default', 'value' => 1],
            [['date', 'contents'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() : array
    {
        return [
            'id' => 'ID',
            'visible'       => Yii::t('app', 'Status'),
            'date'          => Yii::t('app', 'Grade date'),
            'user'          => Yii::t('app', 'Added by'),
            'score'         => Yii::t('app', 'Score'),
            'type'          => Yii::t('app', 'Score type'),
            'contents'      => Yii::t('app', 'Exam contents'),
            'description'   => Yii::t('app', 'Exam description'),
            'calc_studname' => Yii::t('app', 'Student'),
        ];
    }

    public function getFullFileName()
    {
        return Yii::getAlias("@attestates/{$this->calc_studname}/attestate-{$this->id}.pdf");
    }

    public function delete()
    {
        $this->visible = 0;
        if ($this->save(true, ['visible'])) {
            $filePath = $this->getFullFileName();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return true;
        } else {
            return false;
        }
    }

    public function writePdfFile()
    {
        $student = Student::find()->andWhere(['id' => $this->calc_studname])->one();
        if (empty($student)) {
            return false;
        }
        $attestation = $this->toArray();
        $attestation['studentId'] = $student->id;
        $attestation['studentName'] = $student->name;
        unset($attestation['calc_studname']);

        $attestatesDir = Yii::getAlias("@attestates");
        $attestatesDirStudentSubDir = "{$attestatesDir}/{$attestation['studentId']}";
        $filePath = $this->getFullFileName();
        if (!file_exists($attestatesDir)) {
            mkdir($attestatesDir, 0775, true);
        }
        if (!file_exists($attestatesDirStudentSubDir)) {
            mkdir($attestatesDirStudentSubDir, 0775, true);
        }
        $pdf = new Pdf([
            'filename'    => $filePath,
            'mode'        => Pdf::MODE_UTF8,
            'format'      => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_FILE, 
            'content'     => Yii::$app->controller->renderPartial('viewPdf', [
                'attestation'  => $attestation,
                'contentTypes' => self::getExamContentTypes(),
                'exams'        => self::getExams(),
            ]),
            'cssFile'     => '@app/web/css/print_attestate.css',
            'options'     => [
                'title'   => Yii::t('app', 'Attestation'),
            ],
            'marginHeader' => 0,
            'marginFooter' => 0,
            'marginTop'    => 0,
            'marginBottom' => 0,
            'marginLeft'   => 0,
            'marginRight'  => 0,
        ]);
        $pdf->render();

        return true;
    }

    /**
     * Возвращает список оценок студента
     * @param int $id
     * 
     * @return ActiveDataProvider
     */
    public static function getStudentGrades(int $sid) : ActiveDataProvider
    {
        $query = (new \yii\db\Query())
        ->select([
            'id'          => 'sg.id',
            'date'        => 'sg.date',
            'userName'    => 'u.name',
            'score'       => 'sg.score',
            'type'        => 'sg.type',
            'description' => 'sg.description',
            'contents'    => 'sg.contents',
            'studentId'   => 'sg.calc_studname',
            'studentName' => 's.name',
        ])
        ->from(['sg' => self::tableName()])
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

    /**
     * Возвращает список полей содержимого экзамена
     * @param string $exam
     * 
     * @return array
     */
    public static function getExamContents(string $exam) : array
    {
        $result = [];
        switch ($exam) {
            case self::EXAM_YLE_STARTERS:
            case self::EXAM_YLE_MOVERS:
            case self::EXAM_YLE_FLYERS:
            case self::EXAM_KET_A2:
                $result['contents'] = [
                        self::EXAM_CONTENT_LISTENING           => self::getExamContentType(self::EXAM_CONTENT_LISTENING),
                        self::EXAM_CONTENT_READING_AND_WRITING => self::getExamContentType(self::EXAM_CONTENT_READING_AND_WRITING),
                        self::EXAM_CONTENT_SPEAKING            => self::getExamContentType(self::EXAM_CONTENT_SPEAKING),
                ];
                break;
            case self::EXAM_PET_B1:
                $result['contents'] = [
                        self::EXAM_CONTENT_LISTENING => self::getExamContentType(self::EXAM_CONTENT_LISTENING),
                        self::EXAM_CONTENT_READING   => self::getExamContentType(self::EXAM_CONTENT_READING),
                        self::EXAM_CONTENT_WRITING   => self::getExamContentType(self::EXAM_CONTENT_WRITING),
                        self::EXAM_CONTENT_SPEAKING  => self::getExamContentType(self::EXAM_CONTENT_SPEAKING),
                ];
                break;
            case self::EXAM_FCE_B2:
                $result['contents'] = [
                        self::EXAM_CONTENT_LISTENING      => self::getExamContentType(self::EXAM_CONTENT_LISTENING),
                        self::EXAM_CONTENT_READING        => self::getExamContentType(self::EXAM_CONTENT_READING),
                        self::EXAM_CONTENT_WRITING        => self::getExamContentType(self::EXAM_CONTENT_WRITING),
                        self::EXAM_CONTENT_SPEAKING       => self::getExamContentType(self::EXAM_CONTENT_SPEAKING),
                        self::EXAM_CONTENT_USE_OF_ENGLISH => self::getExamContentType(self::EXAM_CONTENT_USE_OF_ENGLISH),
                ];
                break;
            case self::EXAM_TEXT_BOOK_FINAL:
                $result['contents'] = [
                    self::EXAM_CONTENT_TOOK_THE_COURSE    => self::getExamContentType(self::EXAM_CONTENT_TOOK_THE_COURSE),
                    self::EXAM_CONTENT_ACCORDING_TO_BOOK  => self::getExamContentType(self::EXAM_CONTENT_ACCORDING_TO_BOOK),
                    self::EXAM_CONTENT_COURSE_HOURS_COUNT => self::getExamContentType(self::EXAM_CONTENT_COURSE_HOURS_COUNT),
                ];
                break;
            case self::EXAM_OLYMPIAD:
                $result['contents'] = [
                    self::EXAM_CONTENT_TOOK_PART_IN => self::getExamContentType(self::EXAM_CONTENT_TOOK_PART_IN),
                    self::EXAM_CONTENT_BECAME_WHO   => self::getExamContentType(self::EXAM_CONTENT_BECAME_WHO),
                ];
                break;
            case self::EXAM_DICTATION:
                $result['contents'] = [
                    self::EXAM_CONTENT_WROTE_AN   => self::getExamContentType(self::EXAM_CONTENT_WROTE_AN),
                    self::EXAM_CONTENT_BECAME_WHO => self::getExamContentType(self::EXAM_CONTENT_BECAME_WHO),
                ];
                break;
        }
        return $result;
    }
}