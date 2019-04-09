<?php
namespace app\models;
use Yii;
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
                    'show' => true
                ],
                [
                    'id' => 'language',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Language'),
                    'show' => true
                ],
                [
                    'id' => 'hours',
                    'style' => 'width: 10%',
                    'title' => Yii::t('app', 'Hours'),
                    'show' => true
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
                    'style' => 'width: 35%',
                    'title' => Yii::t('app', 'Group'),
                    'show' => true
                ],
                [
                    'id' => 'notes',
                    'style' => 'width: 10%',
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
     * вызывается из StudnameController.php actionView
     * @param integer $id
     * @return array
     */
    public function getStudentSchedule($sid)
    {
        $schedule = (new \yii\db\Query())
        ->select('s.id as lesson_id, gt.id as group_id, gt.calc_service as service_id, srv.name as service, s.calc_denned as day_id, dn.name as day, s.time_begin as begin, s.time_end as end')
        ->from('calc_schedule s')
        ->innerJoin('calc_groupteacher gt', 's.calc_groupteacher=gt.id')
        ->innerJoin('calc_studgroup sg', 'sg.calc_groupteacher=gt.id')
        ->innerJoin('calc_service srv', 'srv.id=gt.calc_service')
        ->innerJoin('calc_denned dn', 'dn.id=s.calc_denned')
        ->where('s.visible=:one AND gt.visible=:one AND sg.visible=:one AND sg.calc_studname=:sid', [':one' => 1, ':sid' => $sid])
        ->orderby(['s.calc_denned' => SORT_ASC, 's.time_begin' => SORT_ASC])
        ->all();
        return $schedule;
    }
    /** возвращает почасовку преподавателей */
    public function getTeacherHours(array $params = [])
    {
        $data = (new \yii\db\Query()) 
        ->select('sch.id as schedule_id,
        t.id as teacher_id,
        t.name as teacher,
        o.id as office_id,
        o.name as office,
        l.id as language_id,
        l.name as language,
        tn.value as hours')
        ->distinct()
        ->from('calc_schedule sch')
        ->innerJoin('calc_groupteacher gt', 'gt.id=sch.calc_groupteacher')
        ->innerJoin('calc_service s', 's.id=gt.calc_service')
        ->innerJoin('calc_timenorm tn', 's.calc_timenorm=tn.id')
        ->innerJoin('calc_lang l', 'l.id=s.calc_lang')
        ->innerJoin('calc_teachergroup tg', 'tg.calc_teacher=sch.calc_teacher')
        ->innerJoin('calc_teacher t', 't.id=sch.calc_teacher')
        ->innerJoin('calc_office o', 'o.id=sch.calc_office')
        ->where([
            'o.visible' => 1,
            'sch.visible' => 1,
        ])
        ->andWhere(['!=', 'sch.calc_groupteacher', 0])
        ->andFilterWhere(['sch.calc_teacher' => $params['tid'] ?? NULL])
        ->andFilterWhere(['sch.calc_office' => $params['oid'] ?? NULL])
        ->orderby(['t.name' => SORT_ASC, 'l.id' => SORT_ASC])
        ->all();

        $lessons = [];
        if (!empty($data)) {
            foreach ($data as $l) {
                if (!isset($lessons[$l['teacher_id']])) {
                    $lessons[$l['teacher_id']] = [
                        'id' => $l['teacher_id'],
                        'teacher' => $l['teacher'],
                        'languages' => [
                            $l['language_id'] => [
                                'name' => $l['language'],
                                'hours' => $l['hours']
                            ]
                        ]
                    ];
                } else {
                    if (!isset($lessons[$l['teacher_id']]['languages'][$l['language_id']])) {
                        $lessons[$l['teacher_id']]['languages'][$l['language_id']] = [
                            'name' => $l['language'],
                            'hours' => $l['hours'] 
                        ];
                    } else {
                        $lessons[$l['teacher_id']]['languages'][$l['language_id']]['hours'] = $lessons[$l['teacher_id']]['languages'][$l['language_id']]['hours'] + $l['hours'];
                    }
                }
            }
        }
        return $lessons;
    }
    public function getScheduleData(array $params = [])
    {
    	$raw_lessons = (new \yii\db\Query()) 
        ->select([
            'id' => 'sd.id',
            'officeId' => 'sd.calc_office',
            'office' => 'o.name',
            'day' => 'sd.calc_denned',
            'room' => 'r.name',
            'time' => 'CONCAT(SUBSTR(sd.time_begin, 1, 5)," - ",SUBSTR(sd.time_end, 1, 5))',
            'teacherId' => 't.id',
            'teacher' => 't.name',
            'groupId' => 'gt.id',
            'group' => 's.name',
            'notes' => 'sd.notes'
        ])
        ->from(['sd' => 'calc_schedule'])
        ->innerJoin(['o' => 'calc_office'], 'o.id = sd.calc_office')
        ->innerJoin(['r' => 'calc_cabinetoffice'], 'r.id = sd.calc_cabinetoffice')
        ->innerJoin(['t' => 'calc_teacher'], 't.id = sd.calc_teacher')
        ->innerJoin(['gt' => 'calc_groupteacher'], 'gt.id = sd.calc_groupteacher')
        ->innerJoin(['s' => 'calc_service'], 's.id = gt.calc_service')
        ->where([
            'o.visible' => 1,
            'sd.visible' => 1
        ])
        ->andWhere(['!=', 'sd.calc_groupteacher', 0])
        ->andFilterWhere(['sd.calc_denned' => $params['did'] ?? NULL])
        ->andFilterWhere(['sd.calc_office' => $params['oid'] ?? NULL])
        ->andFilterWhere(['s.calc_lang' => $params['lid'] ?? NULL])
        ->andFilterWhere(['s.calc_eduform' => $params['fid'] ?? NULL])
        ->andFilterWhere(['s.calc_eduage' => $params['aid'] ?? NULL])
        ->andFilterWhere(['sd.calc_teacher' => $params['tid'] ?? NULL])
        ->orderby([
            'sd.calc_office' => SORT_ASC,
            'sd.calc_denned' => SORT_ASC,
            'r.name' => SORT_ASC,
            'sd.time_begin' => SORT_ASC])
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