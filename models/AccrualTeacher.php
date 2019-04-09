<?php

namespace app\models;

use Yii;
use app\models\Coefficient;
use app\models\Edunormteacher;
use app\models\TeacherLanguagePremium;
/**
 * This is the model class for table "calc_accrualteacher".
 *
 * @property integer $id
 * @property integer $calc_groupteacher
 * @property integer $calc_edunormteacher
 * @property integer $calc_teacher
 * @property integer $user
 * @property string $data
 * @property integer $visible
 * @property string $data_visible
 * @property integer $user_visible
 * @property integer $done
 * @property integer $user_done
 * @property string $data_done
 * @property double $value
 * @property double $value_corp
 * @property double $value_prem
 * @property double $value_transport
 * @property string $value_transport_desc
 */
class AccrualTeacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_accrualteacher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_groupteacher', 'calc_edunormteacher', 'calc_teacher', 'user', 'data', 'visible', 'value', 'value_corp', 'value_prem'], 'required'],
            [['calc_groupteacher', 'calc_edunormteacher', 'calc_teacher', 'user', 'visible', 'user_visible', 'done', 'user_done'], 'integer'],
            [['data', 'data_visible', 'data_done'], 'safe'],
            [['value', 'value_corp', 'value_prem', 'value_transport'], 'number'],
            [['value_transport_desc'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'calc_groupteacher' => Yii::t('app', 'Calc Groupteacher'),
            'calc_edunormteacher' => Yii::t('app', 'Calc Edunormteacher'),
            'calc_teacher' => Yii::t('app', 'Calc Teacher'),
            'user' => Yii::t('app', 'User'),
            'data' => Yii::t('app', 'Data'),
            'visible' => Yii::t('app', 'Visible'),
            'data_visible' => Yii::t('app', 'Data Visible'),
            'user_visible' => Yii::t('app', 'User Visible'),
            'done' => Yii::t('app', 'Done'),
            'user_done' => Yii::t('app', 'User Done'),
            'data_done' => Yii::t('app', 'Data Done'),
            'value' => Yii::t('app', 'Value'),
            'value_corp' => Yii::t('app', 'Value corporative premium'),
            'value_prem' => Yii::t('app', 'Value language premium'),
            'value_transport' => Yii::t('app', 'Value Transport'),
            'value_transport_desc' => Yii::t('app', 'Value Transport Desc'),
        ];
    }

	/**
	 * возвращает коэффициент для расчета начисления (коэффициент зависит от количества учеников посетивших занятие)
	 * @param integer $cnt
	 * @return float
	 */
	protected static function calculateMultiplier($cnt)
	{
        $coefficients = Coefficient::getCoefficientsList();
        $coefs        = $coefficients['data'] ? $coefficients['data'] : [];
        $result       = null;        
        $min          = null;
        $max          = null;
        if ($coefs) {
            foreach($coefs as $coef) {
                if ((int)$coef['studcount'] === (int)$cnt) {
                    $result = $coef['value'];
                }
                if (!$min) {
                    $min = [
                        'cnt'   => $coef['studcount'],
                        'value' => $coef['value']
                    ];
                } else {
                    if ($coef['studcount'] < $min['cnt']) {
                        $min['cnt']   = $coef['studcount'];
                        $min['value'] = $coef['value'];
                    }
                }
                if (!$max) {
                    $max = [
                        'cnt'   => $coef['studcount'],
                        'value' => $coef['value']
                    ];
                } else {
                    if ($coef['studcount'] > $max['cnt']) {
                        $max['cnt']   = $coef['studcount'];
                        $max['value'] = $coef['value'];
                    }
                }
            }

            if (!$result) {
                if ($cnt <= $min['cnt']) {
                    $result = $min['value'];
                }
                if ($cnt >= $max['cnt']) {
                    $result = $max['value'];
                }
            }
        }
		return $result ? $result : 1;
	}

	/**
	 * считает и возвращает стоимость начисления
	 * @param float $accrual
	 * @param array $lesson
	 * @param float $norm
	 * @param float $corp
	 * @return float
	 */
	protected static function calculateLessonAccrual($lesson, $norm, $corp, $langprem = 0) {
		$accrual = 0;
		/* считаем коэффициент в зависимости от количества учеников */
		$koef = self::calculateMultiplier($lesson['pcount']);
		$fullnorm = $norm;
		/* 
		 * задаем полную ставку (ставка + надбавка)
		 * если надбавка больше 0
		 */
		if($lesson['corp'] > 0) {
			/* суммируем со ставкой */
			$fullnorm += $corp;
        }
        /* проверяем что есть надбавка за язык */
        if($langprem) {
            /* суммируем со ставкой */
            $fullnorm += $langprem;
        }
		/* считаем сумму начисления в зависимости от типа учебного времени */
		switch($lesson['edutime']){
			/* дневное время у всех из ставки вычитается 50 рублей */
			case 1: $accrual = ($fullnorm - 50) * $lesson['time'] * $koef; break;
			/* вечернее время используем полную ставку */
			case 2: $accrual = $fullnorm * $lesson['time'] * $koef; break;
		}

		return ['accrual' => $accrual, 'koef' => $koef, 'tax' => $fullnorm, 'prem' => $langprem];
	}

    /**
     * вызывается из карточки преподавателя Teacher/view 
     * считает и возвращает общуюю сумму начислений преподавателя по текущим проверенным и неначисленным занятиям 
	 * @param integer id
	 * @return float
     */
	public static function calculateFullTeacherAccrual(int $id, int $gid = NULL) 
	{
		/* получаем нормы оплаты преподавателя и ставку */
        if (!empty($edunorm = Edunormteacher::getTeacherTaxesForAccrual($id))) {
            /* получаем надбавки за языки */
            $teacherLanguagePremium = new TeacherLanguagePremium();
            $langprem = $teacherLanguagePremium->getTeacherLanguagePremiumsForAccrual($id);
			/* получаем данные по занятиям */
			$list = [$id];
			$order = ['jg.data' => SORT_DESC];
			if (!empty($lessons = self::getViewedLessonList($list, $order, $gid))) {
                /* задаем переменную для подсчета суммы начисления */
                $accrual = 0;
                $lids = [];
                $i = 0;
                $lp = 0;
                foreach ($lessons as $lesson) {
                    $norm = $edunorm['norm'][$lesson['tjplace']] ?? 0;
                    /* 
                     * вызываем функцию расчета стоимости начисления за урок 
                     * и считаем суммарное начисление по всем урокам
                     */
                    $result = self::calculateLessonAccrual(
                        $lesson, 
                        $norm, 
                        $edunorm['corp'],
                        $langprem['prem'][$lesson['lang_id']][$lesson['tjplace']] ?? 0
                    );
                    $accrual += $result['accrual'];
                    $lids[] = $lesson['jid'];
                    $lp = $result['prem'];
                    /* формируем доп данные для возврата в список уроков ожидающих проверки */
                    $lessons[$i]['koef'] = $result['koef'];
                    $lessons[$i]['tax'] = $norm;
                    $lessons[$i]['accrual'] = $result['accrual'];
                    $lessons[$i]['value_corp'] = $lesson['corp'] > 0 ? $edunorm['corp'] : 0;
                    $i++;
                }
                /* возвращаем результат */
                return [
                    'lessons' => $lessons,
                    'accrual' => round($accrual, 2),
                    'norm' => $edunorm['normid'][$lesson['tjplace']] ?? 0,
                    'corp' => $edunorm['corp'],
                    'prem' => $lp,
                    'lids' => $lids
                ];
            } else {
                /* если нет проверенных занятий */
                return [
                    'lessons' => [],
                    'accrual' => 0,
                    'norm' => 0,
                    'corp' => 0,
                    'prem' => 0,
                    'lids' => []
                ];
            }
		} else {
            /* если нет ставок */
			return [
                'lessons' => [],
                'accrual' => 0,
                'norm' => 0,
                'corp' => 0,
                'prem' => 0,
                'lids' => []
            ];
		}
    }

	/**
     * вызывается из отчета по начислениям Report/Accrual 
	 * считает и возвращает начисление для преподавателя за одно занятие
	 * @param array $teachers
	 * @param array $lesson
	 * @return float
	 */
    public static function getLessonFinalCost($teachers, $lesson)
    {
		$accrual = 0;
		foreach($teachers as $t) {
			if((int)$t['id'] === (int)$lesson['tid']) {
                /* получаем надбавки за языки */
                $teacherLanguagePremium = new TeacherLanguagePremium();
                $langprem = $teacherLanguagePremium->getTeacherLanguagePremiumsForAccrual((int)$t['id']);
				$accrual = self::calculateLessonAccrual(
                    $lesson, 
                    $t['value'][$lesson['tjplace']] ?? 0, 
                    $t['vcorp'],
                    $langprem['prem'][$lesson['lang_id']][$lesson['tjplace']] ?? 0
                )['accrual'];
                break;
			}
		}
		
		return $accrual;
	}

    /* возвращает список преподавателей у которых есть занятия к начислению и начисления к выплате */
    private static function getTeachersWithViewedLessons()
    {
        $t_lessons = (new \yii\db\Query()) 
        ->select('t.id as id, t.name as name')
        ->distinct()
        ->from('calc_journalgroup jg')
        ->innerJoin('calc_teacher t', 't.id=jg.calc_teacher')
        ->where('jg.done!=:one AND jg.view=:one AND jg.visible=:one AND t.visible=:one AND t.old!=:one', [':one' => 1]);

        $t_accruals = (new \yii\db\Query()) 
        ->select('t.id as id, t.name as name')
        ->distinct()
        ->from('calc_accrualteacher at')
        ->innerJoin('calc_teacher t', 't.id=at.calc_teacher')
        ->where('at.visible=:one AND at.done!=:one AND t.visible=:one AND t.old!=:one', [':one' => 1]);

        $teachers = (new yii\db\Query())
        ->select('*')
        ->from(['table' => $t_lessons->union($t_accruals)])
        ->orderBy('name')
        ->all();

        return $teachers;
	}

    /* возвращает id преподавателей у которых есть занятия к начислению */
	public static function getTeachersWithViewedLessonsIds()
	{   
		$teachers = [];
		$tmpteachers = self::getTeachersWithViewedLessons();
		
        if(!empty($tmpteachers)) {
    		/* формируем новый массив из перепечатываем id преподавателей */
            $i = 0;
            foreach($tmpteachers as $tmpteacher){
                $teachers[$i]=$tmpteacher['id'];
                $i++;
            }
        }
        
		/* если массив не пустой возвращаем только уникальные id */
	    return !empty($teachers) ? array_unique($teachers) : $teachers;
	}
	
	/* возвращает id и имя преподавателей у которых есть занятия к начислению */
	public static function getTeachersWithViewedLessonsList()
	{   
		$teachers = [];
		$tmpteachers = self::getTeachersWithViewedLessons();
		
        if(!empty($tmpteachers)) {
    		/* формируем новый массив из перепечатываем id преподавателей */
            foreach($tmpteachers as $tmpteacher){
                $teachers[$tmpteacher['id']] = $tmpteacher['name'];
            }
        }

		/* если массив не пустой возвращаем только уникальные id */
	    return !empty($teachers) ? array_unique($teachers) : $teachers;
	}
	
	/* возвращает список преподавателей у которых есть занятия к начислению с доп. информацией */
	public static function getTeachersWithViewedLessonsInfo($list)
	{
        $tmp_teachers = (new \yii\db\Query()) 
        ->select('t.id as id, t.name as name, t.calc_statusjob as stjob, t.value_corp as vcorp, en.value as norm, ent.company as tjplace')
        ->from('calc_teacher t')
        ->leftJoin('calc_edunormteacher as ent', 'ent.calc_teacher=t.id')
        ->leftJoin('calc_edunorm en', 'en.id=ent.calc_edunorm')
        ->where('ent.active=:one AND ent.visible=:one', [':one' => 1])
        ->andWhere(['in','t.id',$list])
        ->orderby(['t.name'=>SORT_ASC])->all();
        
        $teachers = [];

        /* делаем хитрый финт ушами, чтобы получить массив ставок в поле value и избавится от дублирующихся записей */
        foreach($tmp_teachers as $t) {
            $tmp = [];
            /* если в массиве нет ключа преподавателя */
            if (!array_key_exists($t['id'], $teachers)) {
                /* создаем временный массив и туда кладем значение направления и ставки */
                $tmp[$t['tjplace']] = $t['norm'];
                /* копируем инфу по преподавателю в новый массив преподавателей */
                $teachers[$t['id']] = $t;
                /* перезаписываем значение value на массив ставок */
                $teachers[$t['id']]['value'] = $tmp;
            } else {
                /* если преподаватель в массиве есть */
                /* копируем имеющийся массив ставок во временную переменную */
                $tmp = $teachers[$t['id']]['value'];
                /* добавляем в него новую ставку */
                $tmp[$t['tjplace']] = $t['norm'];
                /* записываем результирующий массив в value столбец преподавателя */
                $teachers[$t['id']]['value'] = $tmp;
            }
            unset($tmp);
        }
        return $teachers;
	}
	
	/* возвращает список занятий ожидающих начисления по списку преподавателей */
    public static function getViewedLessonList($list, $order, $gid = NULL)
    {
        /* формируем подзапрос для выборки количество учеников на занятии */
        $SubQuery = (new \yii\db\Query())
        ->select('count(sjg.id)')
        ->from('calc_studjournalgroup sjg')
        ->where('sjg.calc_statusjournal=:one and sjg.calc_journalgroup=jg.id');
        
        /* получаем данные по занятиям ожидающим начисление */
        $lessons = (new \yii\db\Query()) 
		->select('jg.id as jid, jg.data as jdate, jg.calc_groupteacher as gid, s.id as sid, s.name as service, 
		tn.value as time, jg.calc_teacher as tid, el.name as level, o.name as office, jg.description as desc, 
		jg.calc_edutime as edutime, jg.view as view, gt.corp as corp, s.calc_lang as lang_id, gt.company as tjplace')
        ->addSelect(['pcount'=>$SubQuery])
        ->from('calc_journalgroup jg')
        ->leftJoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
        ->leftJoin('calc_service s', 's.id=gt.calc_service')
        ->leftJoin('calc_timenorm tn', 'tn.id=s.calc_timenorm')
        ->leftJoin('calc_edulevel el', 'el.id=gt.calc_edulevel')
        ->leftJoin('calc_office o', 'o.id=gt.calc_office')
        ->where('jg.done=:zero AND jg.view=:one AND jg.visible=:one', [':one' => 1, ':zero' => 0])
        ->andWhere(['in','jg.calc_teacher',$list])
        ->andFilterWhere(['jg.calc_groupteacher' => $gid])
        ->orderby($order)
        ->all();
        
        return $lessons;
	}

    public static function getAccrualsByTeacherList($list)
    {
        $SubQuery = (new \yii\db\Query())
        ->select('SUM(tn.value)')
        ->from('calc_timenorm tn')
        ->leftJoin('calc_service s','s.calc_timenorm=tn.id')
        ->leftJoin('calc_groupteacher gt', 's.id=gt.calc_service')
        ->leftJoin('calc_journalgroup jg', 'jg.calc_groupteacher=gt.id')
        ->where('gt.id=at.calc_groupteacher and jg.calc_accrual=at.id');

        $accruals = (new \yii\db\Query())
        ->select('at.id as aid, t.id as tid, t.name as tname, at.value as value, at.calc_groupteacher as gid')
        ->addSelect(['hours' => $SubQuery])
        ->from('calc_accrualteacher at')
        ->leftJoin('calc_teacher t', 't.id=at.calc_teacher')
        ->where('at.visible=:one and at.done!=:one and t.visible=:one', [':one'=>1])
        ->andFilterWhere(['in', 'at.calc_teacher', $list])
        ->orderby(['at.calc_teacher' => SORT_ASC, 'at.id' => SORT_ASC])
        ->all();
        
        return $accruals;
    }
    
    public static function getAccrualsByTeachers($start = null, $end = null, $teachers = null)
    {
        $accruals = (new \yii\db\Query())
        ->select([
          'id' => 'act.id',
          'teacherId' => 'act.calc_teacher',
          'date' => 'act.data',
          'hours' => 'SUM(tn.value)',
          'tax' => 'act.calc_edunormteacher',
          'sum' => 'act.value'
        ])
        ->from(['act' => self::tableName()])
        ->innerJoin(['gt' => 'calc_groupteacher'], 'gt.id = act.calc_groupteacher')
        ->innerJoin(['s' => 'calc_service'], 's.id = gt.calc_service')
        ->innerJoin(['tn' => 'calc_timenorm'], 'tn.id = s.calc_timenorm')
        ->innerJoin(['j' => 'calc_journalgroup'], 'j.calc_accrual = act.id')
        ->andFilterWhere(['>=', 'act.data', $start])
        ->andFilterWhere(['<=', 'act.data', $end])
        ->andFilterWhere(['in', 'act.calc_teacher', $teachers])
        ->groupBy([
            'act.id',
            'act.calc_teacher',
            'act.data',
            'act.calc_edunormteacher',
            'act.value'
        ])
        ->orderBy([
            'act.calc_teacher' => SORT_ASC,
            'act.data' => SORT_ASC
        ])
        ->all();

        return $accruals;
    }
}
