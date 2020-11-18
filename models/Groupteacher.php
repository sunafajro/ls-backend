<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "calc_groupteacher".
 *
 * @property integer $id
 * @property integer $calc_teacher
 * @property integer $calc_service
 * @property integer $calc_office
 * @property integer $calc_edulevel
 * @property string  $data
 * @property integer $user
 * @property string  $data_visible
 * @property integer $user_visible
 * @property integer $visible
 * @property integer $corp
 * @property integer company
 * 
 * @property Book[]   $books
 * @property Edulevel $eduLevel
 * @property array    $groupBooks
 * @property Office   $office
 * @property Service  $service
 * @property Timenorm $timeNorm
 */
class Groupteacher extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'calc_groupteacher';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['calc_teacher', 'calc_service', 'calc_office', 'calc_edulevel', 'data', 'user', 'visible', 'company'], 'required'],
            [['calc_teacher', 'calc_service', 'calc_office', 'calc_edulevel', 'user', 'user_visible', 'visible', 'corp', 'company'], 'integer'],
            [['data', 'data_visible'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'calc_teacher'  => Yii::t('app', 'Teacher'),
            'calc_service'  => Yii::t('app', 'Service'),
            'calc_office'   => Yii::t('app', 'Office'),
            'calc_edulevel' => Yii::t('app', 'Level'),
            'data'          => Yii::t('app', 'Date'),
            'user'          => 'User',
            'data_visible'  => 'Data Visible',
            'user_visible'  => 'User Visible',
            'visible'       => 'Visible',
            'corp'          => 'Corp',
            'company'       => Yii::t('app', 'Job place')
        ];
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])->viaTable(GroupBook::tableName(), ['group_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEduLevel()
    {
        return $this->hasOne(Edulevel::class, ['id' => 'calc_edulevel']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::class, ['id' => 'calc_office']);
    }

    /**
     * @return ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'calc_service']);
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getTimeNorm()
    {
        return $this->hasOne(Timenorm::class, ['id' => 'calc_timenorm'])->viaTable(Service::tableName(), ['id' => 'calc_service']);
    }

    /**
     * @return array
     */
    public function getGroupBooks()
    {
        return (new \yii\db\Query())
            ->select(['id' => 'gtb.id', 'name' => 'b.name', 'primary' => 'gtb.primary', 'book_id' => 'b.id'])
            ->from(['gtb' => GroupBook::tableName()])
            ->innerJoin(['b' => Book::tableName()], 'b.id = gtb.book_id')
            ->where(['gtb.group_id' => $this->id])
            ->orderby(['gtb.primary' => SORT_DESC, 'b.name' => SORT_ASC])
            ->all();
    }

    /**
     * @return array|bool
     */
    public function getLanguage()
    {
        return (new \yii\db\Query())
            ->select(['id' => 'l.id'])
            ->from(['l' => Lang::tableName()])
            ->leftJoin(['s'  => Service::tableName()], 's.calc_lang = l.id')
            ->leftJoin(['gt' => self::tableName()], 'gt.calc_service = s.id')
            ->where(['gt.id' => $this->id])
            ->one();
    }

    /**
     * Список преподавателей доступных для добавления в группу
     * @param  int   $id
     * @return array
     */
    public static function getTeacherListSimple(int $id) : array
    {
    	$teachers = (new \yii\db\Query())
		->select(['id' => 't.id', 'name' => 't.name'])
		->from(['gt' => self::tableName()])
		->leftJoin(['s'  => Service::tableName()],      's.id = gt.calc_service')
		->leftJoin(['lt' => Langteacher::tableName()],  'lt.calc_lang = s.calc_lang')
		->leftJoin(['t'  => Teacher::tableName()],      't.id = lt.calc_teacher')
		->leftJoin(['tg' => Teachergroup::tableName()], 'tg.calc_teacher = t.id AND tg.calc_groupteacher=gt.id')
		->where([
            'gt.id' => $id,
            't.old' => 0,
            'tg.id' => null
        ])
		->orderby(['t.name' => SORT_ASC])
		->all();

        return ArrayHelper::map($teachers ?? [], 'id', 'name');
    }

    /**
     * Список студентов доступных для добавления в группу
     * @param  int   $id
     * @return array
     */
    public static function getStudentListSimple($id)
    {
        $oid = (int)Yii::$app->session->get('user.ustatus') === 4 ? Yii::$app->session->get('user.uoffice_id') : NULL;
        $students = (new \yii\db\Query())
        ->select(['id' => 's.id', 'name' => 's.name'])
        ->from(['s' => Student::tableName()])
        ->innerJoin(['i'  => Invoicestud::tableName()], 'i.calc_studname=s.id')
        ->innerJoin(['gt' => self::tableName()],        'gt.calc_service=i.calc_service')
        ->leftJoin(['sg'  => Studgroup::tableName()],   's.id = sg.calc_studname and gt.id = sg.calc_groupteacher');
        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
          $students = $students->innerJoin(['so' => 'student_office'], 's.id = so.student_id');
        }
        $students = $students->where([
            'gt.id'     => $id,
            's.visible' => 1,
            's.active'  => 1,
            'i.done'    => 0,
            'i.visible' => 1,
            'sg.id'     => NULL
        ]);
        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
          $students = $students->andWhere(['so.office_id' => $oid ]);
        }
        $students = $students->orderby(['s.name' => SORT_ASC])
        ->all();

        return ArrayHelper::map($students ?? [], 'id', 'name');
    }

    public function getInfo()
	{
		return [];
	}

	/**
	 * Метод возврашает список преподавателей назначенных группе в виде одномерного массива.
     * @param int  $id
     * @param bool $withLabel
     * @return array
	 */
	public static function getGroupTeacherListSimple($id, $withLabel = false)
	{
		return ArrayHelper::map(self::getGroupTeacherList($id, $withLabel) ?? [], 'id', 'name'); 
    }
	
	/**
	 * Метод возврашает список преподавателей назначенных группе, в виде строки. Разделитель запятая.
     * @param Groupteacher|int $group
     * @param string           $divider
     * @param bool             $withLabel
     *
     * @return string
	 */
	public static function getGroupTeacherListString($group, string $divider = ', ', $withLabel = false) : string
	{
        $teachers = self::getGroupTeacherList($group, $withLabel);
        
        $result = [];
        foreach($teachers as $t) {
            $result[] = Html::a($t['name'], ['teacher/view','id' => $t['id']]);
		}
		
		return join($divider, $result);
	}

	/**
	 *  Метод возврашает список учебников назначенных группе, в виде строки. Разделитель запятая.
	 */
	protected static function getGroupBookListString($id)
	{
		$books = (new \yii\db\Query())
        ->select('b.name as name, gtb.prime as prime')
        ->from('calc_groupteacherbook gtb')
        ->leftJoin('calc_schoolbook b', 'b.id=gtb.calc_book')
        ->where('gtb.visible=:one and gtb.calc_groupteacher=:gid', [':gid'=>$id, ':one'=>1])
        ->orderby(['gtb.prime'=>SORT_DESC, 'b.name'=>SORT_ASC])
        ->all();
		
	    $str = '';
		
		if(!empty($books)) {
			foreach($books as $b){
				if($b['prime']){
					$str .= '<span class="label label-primary">' . $b['name'] . '</span>'; 
				} else {
					$str .= $b['name'];
				}
				$str .= ', ';
			}
			
			$str = mb_substr($str, 0, -2) . '.';
		}
		
		return $str;
    }

    /**
     *  Метод возврашает расписание занятий группы, в виде строки. Разделитель запятая.
     * @param $id
     *
     * @return string
     */
	public static function getGroupLessonScheduleString($id)
	{
		$schedule =  (new \yii\db\Query())
        ->select('d.name as day, s.time_begin as start, s.time_end as end')
        ->from(['s' => Schedule::tableName()])
        ->leftJoin('calc_denned d', 'd.id = s.calc_denned')
        ->where('s.visible=:one and s.calc_groupteacher=:gid', [':gid' => $id, ':one' => 1])
        ->orderby(['s.calc_denned'=>SORT_ASC, 's.time_begin'=>SORT_ASC])
        ->all();
		
        $str = '';
		if(!empty($schedule)) {
			foreach($schedule as $s) {
				$str .= $s['day'] . " (" . substr($s['start'], 0, 5) . "-" . substr($s['end'], 0, 5) . "), ";
			}
			
			$str = mb_substr($str, 0, -2) . '.';
		}
		return $str;
    }

    /**
     * Возвращает массив преподавателей назначенных группе
     * @param Groupteacher|int $group
     * @param bool             $withLabel
     *
     * @return array
     * @throws NotFoundHttpException
     */
	protected static function getGroupTeacherList($group, bool $withLabel = false)
	{
	    if (!($group instanceof Groupteacher)) {
	        $id = $group;
            $group = self::find()->andWhere(['id' => $id])->one();
        } else {
            $id = $group->id;
        }
        if (empty($group)) {
            throw new NotFoundHttpException("Группа #{$id} не найдена");
        }
        $teachers = (new \yii\db\Query())
            ->select(['id' => 't.id', 'name' => 't.name'])
            ->from(['tg' => Teachergroup::tableName()])
            ->innerJoin(['t' => Teacher::tableName()], 't.id = tg.calc_teacher')
            ->where([
                't.visible'  => 1,
                'tg.visible' => 1,
                'tg.calc_groupteacher' => $id,
            ])
            ->orderBy(['t.name' => SORT_ASC])
            ->all() ?? [];
        if ($withLabel) {
            if (count($teachers) > 1) {
                foreach ($teachers ?? [] as $key => $teacher) {
                    if ((int)$group->calc_teacher === (int)$teacher['id']) {
                        $teachers[$key]['name'] = $teachers[$key]['name']
                            . ' '
                            . Html::tag('span', null, ['class' => 'fa fa-star', 'aria-hidden' => 'true', 'title' => 'Основной преподаватель']);
                    }
                }
            }
        }
        
        return $teachers;
	}
}
