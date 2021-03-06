<?php

namespace school\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "calc_journalgroup".
 *
 * @property integer $id
 * @property integer $calc_groupteacher
 * @property integer $calc_teacher
 * @property integer $calc_accrual
 * @property integer $calc_edutime
 * @property string  $time_begin
 * @property string  $time_end
 * @property string  $description
 * @property string  $homework
 * @property string  $type
 * @property string  $data
 * @property integer $user
 * @property integer $edit
 * @property integer $user_edit
 * @property string  $data_edit
 * @property integer $view
 * @property string  $data_view
 * @property integer $user_view
 * @property integer $done
 * @property string  $data_done
 * @property integer $user_done
 * @property integer $visible
 * @property string  $data_visible
 * @property integer $user_visible
 * @property integer $audit
 * @property integer $user_audit
 * @property string  $data_audit
 * @property string  $description_audit
 */
class Journalgroup extends ActiveRecord
{
    // время проведения занятия
    const EDUCATION_TIME_WORK     = 1;
    const EDUCATION_TIME_EVENING  = 2;
    /** @deprecated */
    const EDUCATION_TIME_HALFWORK = 3;

    // статусы посещения занятия
    const STUDENT_STATUS_PRESENT         = 1;
    const STUDENT_STATUS_ABSENT_WARNED   = 2;
    const STUDENT_STATUS_ABSENT_UNWARNED = 3;

    // типы занятий
    const TYPE_ONLINE  = 'online';
    const TYPE_OFFICE  = 'office';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_journalgroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user'],    'default', 'value' => Yii::$app->user->identity->id],
            [['visible'], 'default', 'value' => 1],
            [['type'],    'default', 'value' => self::TYPE_OFFICE],
            [['description_audit'], 'default', 'value' => ''],
            [
                [
                    'edit', 'view', 'done', 'audit',
                    'user_edit', 'user_view', 'user_done', 'user_visible', 'user_audit',
                    'calc_accrual',
                ],
                'default', 'value' => 0,
            ],
            [
                [
                    'data_edit', 'data_view', 'data_done', 'data_visible', 'data_audit',
                ],
                'default', 'value' => '0000-00-00',
            ],
            [
                [
                    'calc_groupteacher', 'data', 'user', 'visible',
                    'description', 'homework', 'calc_edutime', 'calc_teacher',
                    'time_begin', 'time_end', 'type',
                ], 'required',
            ],
            [
                [
                    'view', 'user_view', 'calc_groupteacher', 'user',
                    'visible', 'user_visible', 'done', 'user_done',
                    'calc_accrual', 'calc_edutime', 'edit', 'user_edit',
                    'audit', 'user_audit', 'calc_teacher',
                ], 'integer',
            ],
            [['data_view', 'data', 'data_visible', 'data_done', 'data_edit', 'data_audit'], 'safe'],
            [['description', 'homework', 'description_audit', 'time_begin', 'time_end', 'type'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', 'ID'),
            'calc_groupteacher' => Yii::t('app', 'Calc Groupteacher'),
            'calc_teacher'      => Yii::t('app', 'Teacher'),
            'calc_accrual'      => Yii::t('app', 'Calc Accrual'),
            'calc_edutime'      => Yii::t('app', 'Time'),
            'time_begin'        => Yii::t('app', 'Start time'),
            'time_end'          => Yii::t('app', 'End time'),
            'description'       => Yii::t('app', 'Lesson description'),
            'homework'          => Yii::t('app', 'Homework'),
            'type'              => Yii::t('app', 'Type'),
            'data'              => Yii::t('app', 'Date'),
            'user'              => Yii::t('app', 'User'),
            'edit'              => Yii::t('app', 'Edit'),
            'user_edit'         => Yii::t('app', 'User Edit'),
            'data_edit'         => Yii::t('app', 'Data Edit'),
            'view'              => Yii::t('app', 'View'),
            'data_view'         => Yii::t('app', 'Data View'),
            'user_view'         => Yii::t('app', 'User View'),
            'done'              => Yii::t('app', 'Done'),
            'data_done'         => Yii::t('app', 'Data Done'),
            'user_done'         => Yii::t('app', 'User Done'),
            'visible'           => Yii::t('app', 'Visible'),
            'data_visible'      => Yii::t('app', 'Data Visible'),
            'user_visible'      => Yii::t('app', 'User Visible'),
            'audit'             => Yii::t('app', 'Audit'),
            'user_audit'        => Yii::t('app', 'User Audit'),
            'data_audit'        => Yii::t('app', 'Data Audit'),
            'description_audit' => Yii::t('app', 'Description Audit'),
        ];
    }

    /** 
     * Lesson is correct and available for accrual
     * @return bool
     */
    public function view()
    {
        $this->view = 1;
        $this->user_view = Yii::$app->user->identity->id;
        $this->data_view = date('Y-m-d');

        return $this->save(true, ['view', 'user_view', 'data_view']);
    }

    /** 
     * Lesson is need to be checked
     * @return bool
     */
    public function unview()
    {
        $this->view = 0;
        $this->user_view = Yii::$app->user->identity->id;
        $this->data_view = date('Y-m-d');

        return $this->save(true, ['view', 'user_view', 'data_view']);
    }

    public static function getLessonLocationTypes()
    {
        return [
            self::TYPE_ONLINE => 'онлайн',
            self::TYPE_OFFICE => 'в офисе',
        ];
    }

    public static function getEducationTimes()
    {
        return [
            self::EDUCATION_TIME_WORK => 'рабочее (с 9:00 до 17:30 для менеджеров и руководителей)',
            self::EDUCATION_TIME_EVENING => 'вечернее (после 17:30 для преподавателей, менеджеров и руководителей)',
            //self::EDUCATION_TIME_HALFWORK => 'полурабочее время (с 16:00 для руководителей)',
        ];
    }

    public static function getAttendanceScopedStatuses()
    {
        return [
            self::STUDENT_STATUS_PRESENT         => 'присутствовал',
            // (не предупредил)
            self::STUDENT_STATUS_ABSENT_UNWARNED => 'не было',
        ];
    }

    public static function getAttendanceAllStatuses()
    {
        return [
            self::STUDENT_STATUS_PRESENT         => 'присутствовал',
            // (предупредил)
            self::STUDENT_STATUS_ABSENT_WARNED   => 'не было (принес справку)',
            // (не предупредил)
            self::STUDENT_STATUS_ABSENT_UNWARNED => 'не было',
        ];
    }

    public function getCommentsByLesson($id)
    {
        $comments = (new \yii\db\Query())
        ->select([
            'studentId'   => 's.id',
            'studentName' => 's.name',
            'comment'     => 'sc.comments',
            'successes'   => 'sc.successes',
        ])
        ->from(['sc' => Studjournalgroup::tableName()])
        ->innerJoin(['s' => Student::tableName()], 's.id = sc.calc_studname')
        ->where([
            'sc.calc_journalgroup'  => $id,
            'sc.calc_statusjournal' => 1,
        ])
        ->all();
        return $comments;
    }

    public static function getLastLessonTimesByGroup(int $gid): array
    {
        $lessons = (new \yii\db\Query())
        ->select(['begin' => 'time_begin', 'end' => 'time_end'])
        ->from(['j' => self::tableName()])
        ->where([
            'visible' => 1,
            'calc_groupteacher' => $gid,
        ])
        ->andWhere([
            'and',
            [
                'and',
                ['not', ['time_begin' => null]],
                ['not', ['time_begin' => '00:00']],
            ],
            [
                'and',
                ['not', ['time_end' => null]],
                ['not', ['time_end' => '00:00']],
            ]
        ])
        ->limit(10)
        ->orderBy(['id' => SORT_DESC])
        ->indexBy(['begin'])
        ->all();

        return $lessons;
    }

    public static function prepareStudentSuccessesList(int $count)
    {
        $successes = [];
        if ($count > 0) {
            for ($num = 1; $num <= $count; $num++) {
                $successes[] = Html::tag('i', '', ['class' => 'fa fa-ticket', 'aria-hidden' => 'true', 'title' => 'Успешик']);
            }
        }

        return $successes;
    }
}
