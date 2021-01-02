<?php

namespace app\models;

use app\components\helpers\DateHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "calc_accrualteacher".
 *
 * @property integer $id
 * @property integer $calc_groupteacher
 * @property integer $calc_edunormteacher
 * @property integer $calc_teacher
 * @property integer $user
 * @property string  $data
 * @property integer $visible
 * @property string  $data_visible
 * @property integer $user_visible
 * @property integer $done
 * @property integer $user_done
 * @property string  $data_done
 * @property double  $value
 * @property double  $value_corp
 * @property double  $value_prem
 * @property double  $value_transport
 * @property string  $value_transport_desc
 * @property string  $outlay
 */
class AccrualTeacher extends \yii\db\ActiveRecord
{
    // Введены начиная с 01.10.2020
    const EDU_LEVEL_COEFFICIENTS = [
        'A1'    => 1,
        'A2'    => 1,
        'B1'    => 1.1,
        'B2'    => 1.2,
        'C1'    => 1.3,
        'HSK1'  => 1,
        'HSK2'  => 1,
        'HSK3'  => 1.1,
        'HSK4'  => 1.2,
        'HSK5'  => 1.3,
        'ОГЭ'   => 1.1,
        'ЕГЭ'   => 1.2,
        'IELTS' => 1.3,
    ];

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
            [['visible'], 'default', 'value' => 1],
            [['data'],    'default', 'value' => date('Y-m-d')],
            [['user'],    'default', 'value' => Yii::$app->session->get('user.uid')],
            [['calc_groupteacher', 'calc_edunormteacher', 'calc_teacher', 'user', 'data', 'visible', 'value', 'value_corp', 'value_prem'], 'required'],
            [['calc_groupteacher', 'calc_edunormteacher', 'calc_teacher', 'user', 'visible', 'user_visible', 'done', 'user_done'], 'integer'],
            [['data', 'data_visible', 'data_done', 'outlay'], 'safe'],
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
	 * Возвращает коэффициент для расчета начисления (коэффициент зависит от количества учеников посетивших занятие) или 1
	 * @param integer $cnt
     *
	 * @return float
	 */
	private static function calculateMultiplier(int $cnt) : float
	{
        $coefficients = Coefficient::getCoefficientsList()['data'] ?? [];
        $result       = null;        
        $min          = null;
        $max          = null;
        if ($coefficients) {
            foreach ($coefficients as $coefficient) {
                if ((int)$coefficient['studcount'] === (int)$cnt) {
                    $result = $coefficient['value'];
                }
                if (!$min) {
                    $min = [
                        'cnt'   => $coefficient['studcount'],
                        'value' => $coefficient['value']
                    ];
                } else {
                    if ($coefficient['studcount'] < $min['cnt']) {
                        $min['cnt']   = $coefficient['studcount'];
                        $min['value'] = $coefficient['value'];
                    }
                }
                if (!$max) {
                    $max = [
                        'cnt'   => $coefficient['studcount'],
                        'value' => $coefficient['value']
                    ];
                } else {
                    if ($coefficient['studcount'] > $max['cnt']) {
                        $max['cnt']   = $coefficient['studcount'];
                        $max['value'] = $coefficient['value'];
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
     * Возвращает коэфициент согласно наименованию уровня или 1
     * @param string      $levelName
     * @param string|null $date
     *
     * @return float
     */
    private static function getMultiplierByEduLevel(string $levelName, string $date = null) : float
    {
        $value = self::EDU_LEVEL_COEFFICIENTS[$levelName] ?? 1;
        if ($date) {
            if ($date >= '2020-10-01') {
                return $value;
            } else {
                return 1;
            }
        }
        return $value;
    }

    /**
     * считает и возвращает стоимость начисления
     * @param array $lesson
     * @param float $wageRate
     * @param float $corpPremium
     * @param float $languagePremium
     *
     * @return array
     */
    private static function calculateLessonAccrual(array $lesson, float $wageRate, float $corpPremium = 0, float $languagePremium = 0) : array
    {
		$accrual = 0;
		/* считаем коэффициент в зависимости от количества учеников */
		$studentCountRate = self::calculateMultiplier($lesson['studentCount']);
		// Коэффициент вводится начиная с 01.10.2020
		$groupLevelRate = $lesson['date'] >= '2020-10-01' ? self::getMultiplierByEduLevel($lesson['level']) : 1;
		$fullNorm = $wageRate;
		/* 
		 * задаем полную ставку (ставка + надбавка)
		 * если надбавка больше 0
		 */
		if ($lesson['corp'] > 0) {
			/* если корпоративная группа, суммируем надбавку со ставкой */
            $fullNorm += $corpPremium;
        }
        /* проверяем что есть надбавка за язык */
        if ($languagePremium) {
            /* суммируем со ставкой */
            $fullNorm += $languagePremium;
        }

        $dayTimeMarkup = 0;
        $totalValue    = 0;
		/* считаем сумму начисления в зависимости от типа учебного времени */
		switch ($lesson['eduTimeId']) {
			/* дневное время у всех из ставки вычитается 50 рублей */
			case 1:
                $dayTimeMarkup = -50;
			    $totalValue = ($fullNorm + $dayTimeMarkup) * $lesson['time'] * $studentCountRate * $groupLevelRate;
			    break;
			/* вечернее время используем полную ставку */
			case 2:
			    $totalValue = $fullNorm * $lesson['time'] * $studentCountRate * $groupLevelRate;
			    break;
		}

		return [
            'dayTimeMarkup'    => $dayTimeMarkup,
            'groupLevelRate'   => $groupLevelRate,
            'studentCountRate' => $studentCountRate,
            'totalValue'       => $totalValue,
        ];
	}

    /**
     * Считает и возвращает общую сумму начислений преподавателя по текущим проверенным и неначисленным занятиям 
	 * @param int      $id  id преподавателя
     * @param int|null $gid id группы
     * @param int|null $month месяц проведения занятий
     * 
	 * @return array
     */
	public static function calculateFullTeacherAccrual(int $id, int $gid = null, int $month = NULL) : array
	{
        // получаем нормы оплаты преподавателя и корпоративную надбавку
        /** @var Teacher $teacher */
        $teacher     = Teacher::find()->andWhere(['id' => $id])->one();
        $corpPremium = $teacher->value_corp;
        $eduNorms    = Edunormteacher::getTeacherTaxesForAccrual($id);

        $result = [
            'corpPremium'     => 0,
            'languagePremium' => 0,
            'lessons'         => [],
            'totalValue'      => 0,
            'wageRateId'      => 0,
        ];

        if (!empty($eduNorms)) {
            // получаем надбавки за языки
            $languagePremiums = TeacherLanguagePremium::getTeacherLanguagePremiumsForAccrual($id);
			// получаем данные по занятиям
			$list = [$id];
            $order = ['jg.data' => SORT_DESC];
            $lessons = self::getViewedLessonList($list, $order, $gid, DateHelper::getDateRangeByMonth($month));
			if (!empty($lessons)) {
                $totalValue         = 0;
                $languagePremiumSum = 0;
                $corpPremiumSum     = 0;
                $wageRateId         = null;
                foreach ($lessons as $key => $lesson) {
                    if ($gid !== null) {
                        $wageRateId = $eduNorms[$lesson['company']]['id'] ?? 0;
                    }
                    $wageRate = (float)($eduNorms[$lesson['company']]['value'] ?? 0);
                    $languagePremium = $languagePremiums[$lesson['languageId']][$lesson['company']]['value'] ?? 0;
                    $resultByLesson = self::calculateLessonAccrual(
                        $lesson,
                        $wageRate,
                        $corpPremium,
                        $languagePremium
                    );
                    $totalValue         += $resultByLesson['totalValue'];
                    $languagePremiumSum += $languagePremium;
                    $corpPremiumSum     += $lesson['corp'] > 0 ? $corpPremium : 0;
                    /* формируем доп данные для возврата в список уроков ожидающих проверки */
                    $lessons[$key]['corpPremium']      = $lesson['corp'] > 0 ? $corpPremium : 0;
                    $lessons[$key]['dayTimeMarkup']    = $resultByLesson['dayTimeMarkup'];
                    $lessons[$key]['groupLevelRate']   = $resultByLesson['groupLevelRate'];
                    $lessons[$key]['languagePremium']  = $languagePremium;
                    $lessons[$key]['studentCountRate'] = $resultByLesson['studentCountRate'];
                    $lessons[$key]['totalValue']       = round($resultByLesson['totalValue'], 2);
                    $lessons[$key]['wageRate']         = $wageRate;
                }

                $result['corpPremium']     = $corpPremiumSum;
                $result['languagePremium'] = $languagePremiumSum;
                $result['lessons']         = $lessons;
                $result['totalValue']      = round($totalValue, 2);
                $result['wageRateId']      = $wageRateId;
            }
		}

        return $result;
    }

	/**
	 * Считает и возвращает начисление для преподавателя за одно занятие
	 * @param array $teachers
	 * @param array $lesson
     * 
	 * @return float|int
	 */
    public static function getLessonFinalCost(array $teachers, array $lesson) : float
    {
		$accrual = 0;
		foreach ($teachers as $t) {
			if ((int)$t['id'] === (int)$lesson['teacherId']) {
                /* получаем надбавки за языки */
                $langPremiums = TeacherLanguagePremium::getTeacherLanguagePremiumsForAccrual((int)$t['id']);
				$accrual = self::calculateLessonAccrual(
                    $lesson, 
                    $t['value'][$lesson['company']] ?? 0,
                    $t['corp'],
                    $langPremiums[$lesson['languageId']][$lesson['company']]['value'] ?? 0
                )['totalValue'];
                break;
			}
		}
		
		return $accrual;
	}

    /**
     * список преподавателей у которых есть занятия к начислению и начисления к выплате
     * 
     * @return array
     */
    private static function getTeachersWithViewedLessons() : array
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

    /**
     * список id преподавателей у которых есть занятия к начислению и начисления для выплаты
     * 
     * @return array
     */
	public static function getTeachersWithViewedLessonsIds() : array
	{   
		$teachers = ArrayHelper::getColumn(self::getTeachersWithViewedLessons(), 'id');

	    return array_unique($teachers);
	}
	
	/**
     * массив id => name преподавателей у которых есть занятия к начислению и начисления для выплаты
     * 
     * @return array
     */
	public static function getTeachersWithViewedLessonsList() : array
	{   
		return ArrayHelper::map(self::getTeachersWithViewedLessons(), 'id', 'name');
	}
	
	/**
     * список преподавателей у которых есть занятия к начислению с доп. информацией
     * @param int[] $list
     * 
     * @return array
     */
	public static function getTeachersWithViewedLessonsInfo(array $list) : array
	{
        $rawTeachers = (new \yii\db\Query())
        ->select([
            'id'        => 't.id',
            'name'      => 't.name',
            'jobStatus' => 't.calc_statusjob',
            'corp'      => 't.value_corp',
            'wageRate'  => 'en.value',
            'company'   => 'ent.company',
        ])
        ->from(['t' => Teacher::tableName()])
        ->leftJoin('calc_edunormteacher as ent', 'ent.calc_teacher = t.id')
        ->leftJoin(['en' => Edunorm::tableName()], 'en.id = ent.calc_edunorm')
        ->where([
            'ent.active'  => 1,
            'ent.visible' => 1
        ])
        ->andWhere(['in', 't.id', $list])
        ->orderby(['t.name' => SORT_ASC])->all();
        
        $teachers = [];
        foreach($rawTeachers as $teacher) {
            $tmp = [];
            if (!isset($teachers[$teacher['id']])) {
                $teachers[$teacher['id']] = $teacher;
            } else {
                $tmp = $teachers[$teacher['id']]['value'];
            }
            $tmp[$teacher['company']] = $teacher['wageRate'];
            $teachers[$teacher['id']]['value'] = $tmp;
        }
        return $teachers;
	}
	
	/**
     * список занятий ожидающих начисления по списку преподавателей
     * @param int[]         $list      массив id преподавателей
     * @param array         $order     параметры сортировки
     * @param int|null      $gid
     * @param string[]|null $dateRange
     * 
     * @return array
     */
    public static function getViewedLessonList(array $list, array $order, int $gid = NULL, array $dateRange = null) : array
    {
        $dateRangeExpression = ['and',
            ['>=', 'jg.data', $dateRange[0] ?? null],
            ['<=', 'jg.data', $dateRange[1] ?? null],
        ];
        /* формируем подзапрос для выборки количество учеников на занятии */
        $SubQuery = (new \yii\db\Query())
        ->select('count(sjg.id)')
        ->from(['sjg' => Studjournalgroup::tableName()])
        ->where('sjg.calc_statusjournal = 1 and sjg.calc_journalgroup = jg.id');
        
        /* получаем данные по занятиям ожидающим начисление */
        return (new \yii\db\Query())
            ->select([
                'id'          => 'jg.id',
                'date'        => 'jg.data',
                'groupId'     => 'jg.calc_groupteacher',
                'serviceId'   => 's.id',
                'service'     => 's.name',
                'time'        => 'tn.value',
                'teacherId'   => 'jg.calc_teacher',
                'level'       => 'el.name',
                'office'      => 'o.name',
                'description' => 'jg.description',
                'eduTimeId'   => 'jg.calc_edutime',
                'viewed'      => 'jg.view',
                'corp'        => 'gt.corp',
                'languageId'  => 's.calc_lang',
                'company'     => 'gt.company',
            ])
            ->addSelect(['studentCount' => $SubQuery])
            ->from(['jg' => Journalgroup::tableName()])
            ->leftJoin(['gt' => Groupteacher::tableName()], 'gt.id = jg.calc_groupteacher')
            ->leftJoin(['s'  => Service::tableName()], 's.id = gt.calc_service')
            ->leftJoin(['tn' => Timenorm::tableName()], 'tn.id = s.calc_timenorm')
            ->leftJoin(['el' => Edulevel::tableName()], 'el.id = gt.calc_edulevel')
            ->leftJoin(['o'  => Office::tableName()], 'o.id = gt.calc_office')
            ->where([
                'jg.done'    => 0,
                'jg.view'    => 1,
                'jg.visible' => 1
            ])
            ->andWhere(['in', 'jg.calc_teacher', $list])
            ->andFilterWhere(['jg.calc_groupteacher' => $gid])
            ->andFilterWhere($dateRangeExpression)
            ->orderby($order)
            ->all();
	}

    /**
     * список доступных начислений по преподавателям
     * @param int[] $list
     * 
     * @return array
     */
    public static function getAccrualsByTeacherList(array $list) : array
    {
        $SubQuery = (new \yii\db\Query())
            ->select('SUM(tn.value)')
            ->from('calc_timenorm tn')
            ->leftJoin('calc_service s','s.calc_timenorm=tn.id')
            ->leftJoin('calc_groupteacher gt', 's.id=gt.calc_service')
            ->leftJoin('calc_journalgroup jg', 'jg.calc_groupteacher=gt.id')
            ->where('gt.id=at.calc_groupteacher and jg.calc_accrual=at.id');

        $accruals = (new \yii\db\Query())
            ->select('at.id as aid, at.data as date, t.id as tid, t.name as tname, at.value as value, at.calc_groupteacher as gid')
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
