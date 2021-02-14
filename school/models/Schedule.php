<?php
namespace school\models;

use common\components\helpers\DateHelper;
use school\models\Report;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "calc_schedule".
 *
 * @property integer $id
 * @property integer $calc_teacher
 * @property integer $calc_groupteacher
 * @property integer $calc_office
 * @property integer $calc_cabinetoffice
 * @property integer $calc_denned
 * @property string $time_begin
 * @property string $time_end
 * @property integer $visible
 * @property integer $user
 * @property string $data
 * @property string $notes
 */
class Schedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_schedule';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_teacher', 'calc_groupteacher', 'calc_office', 'calc_cabinetoffice', 'calc_denned', 'time_begin', 'time_end', 'visible', 'user', 'data'], 'required'],
            [['calc_teacher', 'calc_groupteacher', 'calc_office', 'calc_cabinetoffice', 'calc_denned', 'visible', 'user'], 'integer'],
            [['notes'], 'string'],
            [['time_begin', 'time_end', 'data'], 'safe']
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calc_teacher' => Yii::t('app','Teacher'),
            'calc_groupteacher' => Yii::t('app','Group'),
            'calc_office' => Yii::t('app','Office'),
            'calc_cabinetoffice' => Yii::t('app','Room'),
            'calc_denned' => Yii::t('app','Day of week'),
            'time_begin' => Yii::t('app','Start time'),
            'time_end' => Yii::t('app','End time'),
            'visible' => Yii::t('app', 'Visible'),
            'user' => Yii::t('app', 'User'),
            'data' => Yii::t('app', 'Date'),
            'notes' => Yii::t('app', 'Notes')
        ];
    }
    /* возвращает заголовки столбцов таблицы расписания */
    public function getTableColumns($type = null)
    {
        if ($type === 'hours') {
            return [
                [
                    'id' => 'teacher',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Teacher'),
                    'show' => true,
                ],
                [
                    'id' => 'language',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Language'),
                    'show' => true,
                ],
                [
                    'id' => 'hoursByService',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Hours') . ' (усл.)',
                    'show' => true,
                ],
                [
                    'id' => 'hoursBySchedule',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Hours') . ' (расп.)',
                    'show' => true,
                ],
                [
                    'id' => 'actualHours',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Hours') . ' (факт.)',
                    'show' => true,
                ]
            ];
        } else {
            return [
                [
                    'id' => 'id',
                    'style' => '',
                    'title' => '№',
                    'show' => false
                ],
                [
                    'id' => 'officeId',
                    'style' => '',
                    'title' => Yii::t('app', 'Office Id'),
                    'show' => false
                ],
                [
                    'id' => 'office',
                    'style' => '',
                    'title' => Yii::t('app', 'Office'),
                    'show' => false
                ],
                [
                    'id' => 'day',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Day'),
                    'show' => true
                ],
                [
                    'id' => 'roomId',
                    'style' => '',
                    'title' => Yii::t('app', 'Room Id'),
                    'show' => false
                ],
                [
                    'id' => 'room',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Room'),
                    'show' => true
                ],
                [
                    'id' => 'time',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Time'),
                    'show' => true
                ],
                [
                    'id' => 'teacherId',
                    'style' => '',
                    'title' => Yii::t('app', 'Teacher Id'),
                    'show' => false
                ],
                [
                    'id' => 'teacher',
                    'style' => 'width: 20%',
                    'title' => Yii::t('app', 'Teacher'),
                    'show' => true
                ],
                [
                    'id' => 'groupId',
                    'style' => '',
                    'title' => Yii::t('app', 'Group Id'),
                    'show' => false
                ],
                [
                    'id' => 'group',
                    'style' => 'width: 30%',
                    'title' => Yii::t('app', 'Group'),
                    'show' => true
                ],
                [
                    'id' => 'notes',
                    'style' => 'width: 15%',
                    'title' => Yii::t('app', 'Notes'),
                    'show' => true
                ],
                [
                    'id' => 'actions',
                    'style' => 'width: 5%; text-align: center',
                    'title' => Yii::t('app', 'Act.'),
                    'show' => true
                ],
            ];
        }
    }
    /**
     * метод возвращает расписание занятий студента
     * @param integer $id
     * 
     * @return array
     */
    public function getStudentSchedule(int $sid) : array
    {
        $schedule = (new \yii\db\Query())
        ->select([
            'lesson_id'  => 'sch.id',
            'group_id'   => 'gt.id',
            'service_id' => 'gt.calc_service',
            'service'    => 's.name',
            'day_id'     => 'sch.calc_denned',
            'day'        => 'dn.name',
            'begin'      => 'sch.time_begin',
            'end'        => 'sch.time_end',
        ])
        ->from(['sch' => self::tableName()])
        ->innerJoin(['gt' => Groupteacher::tableName()], 'sch.calc_groupteacher = gt.id')
        ->innerJoin(['sg' => Studgroup::tableName()], 'sg.calc_groupteacher = gt.id')
        ->innerJoin(['s' => Service::tableName()], 's.id = gt.calc_service')
        ->innerJoin(['dn' => 'calc_denned'], 'dn.id = sch.calc_denned')
        ->where([
            'sch.visible'      => 1,
            'gt.visible'       => 1,
            'sg.visible'       => 1,
            'sg.calc_studname' => $sid,
        ])
        ->orderby([
            'sch.calc_denned' => SORT_ASC,
            'sch.time_begin'  => SORT_ASC,
        ])
        ->all();

        return $schedule;
    }

    /**
     * возвращает почасовку преподавателей
     * @param array $params
     * 
     * @return array
     */
    public function getTeacherHours(array $params = []) : array
    {
        $scht = 'sch';
        $gt   = 'g';
        $st   = 's';
        $tnt  = 'tn';
        $lt   = 'l';
        $tt   = 't';
        $ot   = 'o';
        $data = (new \yii\db\Query()) 
            ->select([
                'schedule_id' => "{$scht}.id",
                'teacher_id'  => "{$scht}.calc_teacher",
                'teacher'     => "{$tt}.name",
                'office_id'   => "{$scht}.calc_office",
                'office'      => "{$ot}.name",
                'language_id' => "{$st}.calc_lang",
                'language'    => "{$lt}.name",
                'hours'       => 'tn.value',
                'period'      => "CONCAT({$scht}.time_begin,' - ', {$scht}.time_end)",
            ])
        ->from([$scht => self::tableName()])
        ->innerJoin([$gt => Groupteacher::tableName()], "{$gt}.id = {$scht}.calc_groupteacher")
        ->innerJoin([$st => Service::tableName()], "{$st}.id = {$gt}.calc_service")
        ->innerJoin([$tnt => Timenorm::tableName()], "{$st}.calc_timenorm = {$tnt}.id")
        ->innerJoin([$lt => Lang::tableName()], "{$lt}.id = {$st}.calc_lang")
        ->innerJoin([$tt => Teacher::tableName()], "{$tt}.id = {$scht}.calc_teacher")
        ->innerJoin([$ot => Office::tableName()], "{$ot}.id = {$scht}.calc_office")
        ->where([
            "{$ot}.visible"   => 1,
            "{$scht}.visible" => 1,
        ])
        ->andWhere(['!=', "{$scht}.calc_groupteacher", 0])
        ->andFilterWhere(["{$scht}.calc_teacher" => $params['tid'] ?? NULL])
        ->andFilterWhere(["{$scht}.calc_office" => $params['oid'] ?? NULL])
        ->orderby(["{$tt}.name" => SORT_ASC, "{$lt}.id" => SORT_ASC])
        ->all();

        $lessons = [];
        if (!empty($data)) {
            list('hours' => $lessonHours) = (new Report())->getTeacherHours([
                'start' => DateHelper::getStartOfWeek(-1, true),
                'end'   => DateHelper::getEndOfWeek(-1, true),
            ]);
            foreach ($data ?? [] as $l) {
                if (!isset($lessons[$l['teacher_id']])) {
                    $lessons[$l['teacher_id']] = [
                        'id' => $l['teacher_id'],
                        'teacher' => $l['teacher'],
                        'languages' => [
                            $l['language_id'] => [
                                'name'            => $l['language'],
                                'hoursByService'  => (float)$l['hours'],
                                'hoursBySchedule' => DateHelper::strIntervalToCount($l['period'], ' - ', 'H:i:s', 'h'),
                            ]
                        ],
                        'actualHours' => 0,
                    ];
                } else {
                    if (!isset($lessons[$l['teacher_id']]['languages'][$l['language_id']])) {
                        $lessons[$l['teacher_id']]['languages'][$l['language_id']] = [
                            'name' => $l['language'],
                            'hoursByService'  => (float)$l['hours'],
                            'hoursBySchedule' => DateHelper::strIntervalToCount($l['period'], ' - ', 'H:i:s', 'h'),
                        ];
                    } else {
                        $lessons[$l['teacher_id']]['languages'][$l['language_id']]['hoursByService']  += $l['hours'];
                        $lessons[$l['teacher_id']]['languages'][$l['language_id']]['hoursBySchedule'] += DateHelper::strIntervalToCount($l['period'], ' - ', 'H:i:s', 'h');
                    }
                }
            }
            foreach ($lessonHours ?? [] as $dayDate => $dayData) {
                foreach ($dayData ?? [] as $teacherId => $teacherData) {
                    foreach ($teacherData ?? [] as $lessonsData) {
                        if (isset($lessons[$teacherId]['actualHours'])) {
                            $lessons[$teacherId]['actualHours'] += $lessonsData['periodHours'] ?? 0;
                        }
                    }
                }
            }
            ArrayHelper::multisort($lessons, ['teacher'], [SORT_ASC]);
        }

        return $lessons;
    }

    /**
     * возвращает расписание
     * @param array $params
     * 
     * @return array
     */
    public function getScheduleData(array $params = [])
    {
    	$raw_lessons = (new \yii\db\Query()) 
        ->select([
            'id'        => 'sd.id',
            'officeId'  => 'sd.calc_office',
            'office'    => 'o.name',
            'day'       => 'sd.calc_denned',
            'roomId'    => 'r.id',
            'room'      => 'r.name',
            'time'      => 'CONCAT(SUBSTR(sd.time_begin, 1, 5)," - ",SUBSTR(sd.time_end, 1, 5))',
            'teacherId' => 't.id',
            'teacher'   => 't.name',
            'groupId'   => 'gt.id',
            'group'     => 's.name',
            'notes'     => 'sd.notes'
        ])
        ->from(['sd' => self::tableName()])
        ->innerJoin(['o' => Office::tableName()], 'o.id = sd.calc_office')
        ->innerJoin(['r' => 'calc_cabinetoffice'], 'r.id = sd.calc_cabinetoffice')
        ->innerJoin(['t' => Teacher::tableName()], 't.id = sd.calc_teacher')
        ->innerJoin(['gt' => Groupteacher::tableName()], 'gt.id = sd.calc_groupteacher')
        ->innerJoin(['s' => Service::tableName()], 's.id = gt.calc_service')
        ->where([
            'o.visible' => 1,
            'sd.visible' => 1
        ])
        ->andWhere(['!=', 'sd.calc_groupteacher', 0])
        ->andFilterWhere(['sd.calc_denned'  => $params['did'] ?? NULL])
        ->andFilterWhere(['sd.calc_office'  => $params['oid'] ?? NULL])
        ->andFilterWhere(['s.calc_lang'     => $params['lid'] ?? NULL])
        ->andFilterWhere(['s.calc_eduform'  => $params['fid'] ?? NULL])
        ->andFilterWhere(['s.calc_eduage'   => $params['aid'] ?? NULL])
        ->andFilterWhere(['sd.calc_teacher' => $params['tid'] ?? NULL])
        ->orderby([
            'sd.calc_office' => SORT_ASC,
            'sd.calc_denned' => SORT_ASC,
            'r.name'         => SORT_ASC,
            'sd.time_begin'  => SORT_ASC])
        ->all();
        $lessons = [];
        if ($raw_lessons && count($raw_lessons)) {
            foreach($raw_lessons as $l) {
              if (!isset($lessons[$l['officeId']])) {
                $lessons[$l['officeId']] = [
                    'name' => $l['office'],
                    'rows' => []
                ];
              }
              $lessons[$l['officeId']]['rows'][] = $l;
            }
        }

        return $lessons;
    }
}