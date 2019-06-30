<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
/**
 * This is the model class for table "calc_groupteacher".
 *
 * @property integer $id
 * @property integer $calc_teacher
 * @property integer $calc_service
 * @property integer $calc_office
 * @property integer $calc_edulevel
 * @property string $data
 * @property integer $user
 * @property string $data_visible
 * @property integer $user_visible
 * @property integer $visible
 * @property integer $corp
 * @property integer company
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
            'url' => ['groupteacherbook/create','gid' => $id],
            'options' => ['class' => 'btn btn-block' . (('groupteacherbook/create' == $request) ? ' btn-primary' : ' btn-default')],         
        ];

        return $items;
    }

    public static function getTeacherListSimple($id)
    {
    	$teachers = (new \yii\db\Query())
		->select('t.id as id, t.name as name')
		->from('calc_groupteacher gt')
		->leftJoin('calc_service s', 's.id=gt.calc_service')
		->leftJoin('calc_langteacher lt', 'lt.calc_lang=s.calc_lang')
		->leftJoin('calc_teacher t', 't.id=lt.calc_teacher')
		->leftJoin('calc_teachergroup tg','tg.calc_teacher=t.id AND tg.calc_groupteacher=gt.id')
		->where('gt.id=:group AND t.old=:old AND tg.id is null', [':group' => $id, ':old' => 0])
		->orderby(['t.name' => SORT_ASC])
		->all();

        if(!empty($teachers)) {
            foreach($teachers as $teacher){
                $tmpTeachers[$teacher['id']] = $teacher['name'];
            }
            $teachers = array_unique($tmpTeachers);
        }

        return $teachers;
    }

    public static function getStudentListSimple($id)
    {
        $oid = (int)Yii::$app->session->get('user.ustatus') === 4 ? Yii::$app->session->get('user.uoffice_id') : NULL;
        $students = (new \yii\db\Query())
        ->select(['id' => 's.id', 'name' => 's.name'])
        ->from(['s' => 'calc_studname'])
        ->innerJoin('calc_invoicestud i', 'i.calc_studname=s.id')
        ->innerJoin('calc_groupteacher gt', 'gt.calc_service=i.calc_service')
        ->leftJoin('calc_studgroup sg', 's.id=sg.calc_studname and gt.id=sg.calc_groupteacher');
        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
          $students = $students->innerJoin('student_office so', 's.id=so.student_id');
        }
        $students = $students->where([
            'gt.id' => $id,
            's.visible' => 1,
            's.active' => 1,
            'i.done' => 0,
            'i.visible' => 1,
            'sg.id' =>  NULL
        ]);
        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
          $students = $students->andWhere(['so.office_id' => $oid ]);
        }
        $students = $students->orderby(['s.name' => SORT_ASC])
        ->all();

        if(!empty($students)) {
            foreach($students as $student){
                $tmpStudents[$student['id']] = $student['name'];
            }
            $students = array_unique($tmpStudents);
        }

        return $students;
    }

    public static function getGroupInfoById($id)
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
        ->where('cgt.id=:id', [':id'=>$id])
        ->one();
		
		$result = [];
		
		if(!empty($data)) {
			$result[Yii::t('app', 'Service')] = $data['sname'];
			$result[Yii::t('app', 'Level')] = $data['elname'];
			$result[Yii::t('app', 'Teacher')] = self::getGroupTeacherListString($id);
			$result[Yii::t('app', 'Office')] = $data['oname'];
			$result[Yii::t('app', 'Start date')] = '<span class="label label-default">' . date('d.m.Y', strtotime($data['gdate'])) . '</span>';
			$result[Yii::t('app', 'Books')] = self::getGroupBookListString($id);
			if($data['gvisible'] != 0) {
			    $result[Yii::t('app', 'Status')] = '<span class="label label-success">' . Yii::t('app', 'Active') . '</span>';
				$result[Yii::t('app', 'Schedule')] = self::getGroupLessonScheduleString($id);
		    } else {
				$result[Yii::t('app', 'State')] = '<span class="label label-danger">' . Yii::t('app', 'Finished') . '</span>';
			}
			$result[Yii::t('app', 'Duration')] = $data['tnvalue'] . ' ч.';
			$result[Yii::t('app', 'Job place')] = '<span class="label ' . ((int)$data['direction'] === 1 ? 'label-success' : 'label-info' ) . '">' . $jobPlace[$data['direction']] . '</span>';
		}
			
		return $result;
	}

	/**
	 *  Метод возврашает список преподавателей назначенных группе в виде одномерного массива.
	 */
	public static function getGroupTeacherListSimple($id)
	{
		$data = self::getGroupTeacherList($id);
		
		$teachers = [];
		
		if(!empty($data)) {
		    foreach($data as $d) {
				$teachers[$d['tid']] = $d['tname'];
			}
			
			$teachers = array_unique($teachers);
		}
		
		return $teachers; 
	}
	
	/**
	 *  Метод возврашает список преподавателей назначенных группе, в виде строки. Разделитель запятая.
	 */
	protected static function getGroupTeacherListString($id)
	{
        $teachers = self::getGroupTeacherList($id);
        
        $str = '';
		
		if(!empty($teachers)) {
			foreach($teachers as $t) {
				$str .= Html::a($t['tname'],['teacher/view','id'=>$t['tid']]) . ', ';
			}
			
			$str = mb_substr($str, 0, -2) . '.';
		}
		
		return $str;
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
	 *  Возвращает массив перподавателей назначенных группе
	 */
	protected static function getGroupTeacherList($id)
	{
		$teachers = (new \yii\db\Query())
        ->select('tg.calc_teacher as tid, t.name as tname')
        ->from('calc_teachergroup tg')
        ->innerJoin('calc_teacher t', 't.id=tg.calc_teacher')
        ->where('tg.calc_groupteacher=:gid and t.visible=:one and tg.visible=:one', [':gid'=>$id, ':one'=>1])
        ->orderby(['t.name'=>SORT_ASC])
        ->all();
		
		return $teachers;
	}
	
	
}
