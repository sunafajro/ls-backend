<?php

namespace app\models;

use Yii;
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
 * @property Book[] $books
 */
class Groupteacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_groupteacher';
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calc_teacher' => Yii::t('app', 'Teacher'),
            'calc_service' => Yii::t('app', 'Service'),
            'calc_office' => Yii::t('app', 'Office'),
            'calc_edulevel' => Yii::t('app', 'Level'),
            'data' => Yii::t('app', 'Date'),
            'user' => 'User',
            'data_visible' => 'Data Visible',
            'user_visible' => 'User Visible',
            'visible' => 'Visible',
            'corp' => 'Corp',
            'company' => Yii::t('app', 'Job place')
        ];
    }

    public function getBooks()
    {
        $this->hasMany(Book::class, ['id' => 'book_id'])->viaTable(GroupBook::tableName(), ['group_id' => 'id']);
    }

    public static function getGroupStateById($id)
    {
        $state = NULL;
        
        if(($model = self::findOne($id)) !== NULL) {
           $state =  $model->visible;
        }
        
        return $state;
    }
    public static function getMenuItemList($id, $request)
    {
        
        $items[] = [
            'title' => Yii::t('app','Journal'),
            'url' => ['groupteacher/view','id' => $id],
            'options' => ['class' => 'btn btn-block' . (('groupteacher/view' == $request) ? ' btn-primary' : ' btn-default')],
        ];
        $items[] = [
            'title' => Yii::t('app','Students'),
            'url' => ['groupteacher/addstudent','gid' => $id],
            'options' => ['class' => 'btn btn-block' . (('groupteacher/addstudent' == $request) ? ' btn-primary' : ' btn-default')],           
        ];
        $items[] = [
            'title' => Yii::t('app','Teachers'),
            'url' => ['groupteacher/addteacher','gid' => $id],
            'options' => ['class' => 'btn btn-block' . (('groupteacher/addteacher' == $request) ? ' btn-primary' : ' btn-default')],         
        ];
        $items[] = [
            'title' => Yii::t('app','Books'),
            'url' => ['group-book/create','gid' => $id],
            'options' => ['class' => 'btn btn-block' . (('group-book/create' == $request) ? ' btn-primary' : ' btn-default')],         
        ];

        return $items;
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
		$jobPlace = [ 1 => 'ШИЯ', 2 => 'СРР' ];
        // получаем информацию о группе
        $data =  (new \yii\db\Query())
        ->select('cgt.id as gid, cs.name as sname, ct.id as tid, ct.name as tname, cel.name as elname, cgt.data as gdate, co.name as oname, ctn.value as tnvalue, cgt.visible as gvisible, cgt.company as direction')
        ->from('calc_groupteacher cgt')
        ->leftJoin('calc_teacher ct', 'ct.id=cgt.calc_teacher')
        ->leftJoin('calc_edulevel cel', 'cel.id=cgt.calc_edulevel')
        ->leftJoin('calc_service cs', 'cs.id=cgt.calc_service')
        ->leftJoin('calc_timenorm ctn', 'ctn.id=cs.calc_timenorm')
        ->leftJoin('calc_office co', 'co.id=cgt.calc_office')
        ->where('cgt.id=:id', [':id' => $this->id])
        ->one();
		
		$result = [];
		
		if(!empty($data)) {
			$result[Yii::t('app', 'Service')] = $data['sname'];
			$result[Yii::t('app', 'Level')] = $data['elname'];
			$result[Yii::t('app', 'Teacher')] = static::getGroupTeacherListString($this->id);
			$result[Yii::t('app', 'Office')] = $data['oname'];
			$result[Yii::t('app', 'Start date')] = '<span class="label label-default">' . date('d.m.Y', strtotime($data['gdate'])) . '</span>';
            $result[Yii::t('app', 'Books')] = join(', ', ArrayHelper::getColumn($this->books ?? [], 'name'));
			if($data['gvisible'] != 0) {
			    $result[Yii::t('app', 'Status')] = '<span class="label label-success">' . Yii::t('app', 'Active') . '</span>';
				$result[Yii::t('app', 'Schedule')] = static::getGroupLessonScheduleString($this->id);
		    } else {
				$result[Yii::t('app', 'State')] = '<span class="label label-danger">' . Yii::t('app', 'Finished') . '</span>';
			}
			$result[Yii::t('app', 'Duration')] = $data['tnvalue'] . ' ч.';
			$result[Yii::t('app', 'Job place')] = '<span class="label ' . ((int)$data['direction'] === 1 ? 'label-success' : 'label-info' ) . '">' . $jobPlace[$data['direction']] . '</span>';
		}
			
		return $result;
	}

	/**
	 * Метод возврашает список преподавателей назначенных группе в виде одномерного массива.
     * @param  int   $id
     * @return array
	 */
	public static function getGroupTeacherListSimple($id)
	{
		return ArrayHelper::map(self::getGroupTeacherList($id) ?? [], 'id', 'name'); 
    }
	
	/**
	 * Метод возврашает список преподавателей назначенных группе, в виде строки. Разделитель запятая.
     * @param  int    $id
     * @param  string $divider
     * @return string
	 */
	public static function getGroupTeacherListString(int $id, string $divider = ', ') : string
	{
        $teachers = self::getGroupTeacherList($id);
        
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
	 */
	protected static function getGroupLessonScheduleString($id)	
	{
		$schedule =  (new \yii\db\Query())
        ->select('d.name as day, s.time_begin as start, s.time_end as end')
        ->from('calc_schedule s')
        ->leftJoin('calc_denned d', 'd.id=s.calc_denned')
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
	 * Возвращает массив перподавателей назначенных группе
     * @param  int   $id
     * @return array
	 */
	protected static function getGroupTeacherList($id)
	{
        $group = self::findOne($id);
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
        if (count($teachers) > 1) {
            foreach ($teachers ?? [] as $key => $teacher) {
                if ((int)$group->calc_teacher === (int)$teacher['id']) {
                    $teachers[$key]['name'] = $teachers[$key]['name']
                        . ' '
                        . Html::tag('span', null, ['class' => 'fa fa-star', 'aria-hidden' => 'true', 'title' => 'Основной преподаватель']);
                }
            }
        }
        
        return $teachers;
	}
}
