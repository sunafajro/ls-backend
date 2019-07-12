<?php

namespace app\controllers;

use Yii;
use app\models\AccrualTeacher;
use app\models\Invoicestud;
use app\models\Moneystud;
use app\models\Office;
use app\models\Report;
use app\models\Sale;
use app\models\Schedule;
use app\models\Student;
use app\models\Teacher;
use app\models\Tool;
use app\models\User;
use app\models\search\LessonSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\data\Pagination;


class ReportController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'accrual',
                    'common',
                    'debt',
                    'index',
                    'invoices',
                    'journals',
                    'margin',
                    'payments',
                    'plan',
                    'sale',
                    'salaries',
                    'teacher-hours',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'accrual',
                            'common',
                            'debt',
                            'index',
                            'invoices',
                            'journals',
                            'margin',
                            'plan',
                            'payments',
                            'sale',
                            'salaries',
                            'teacher-hours',
                        ],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'accrual',
                            'common',
                            'debt',
                            'index',
                            'invoices',
                            'journals',
                            'margin',
                            'plan',
                            'payments',
                            'sale',
                            'salaries',
                            'teacher-hours',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    
    /**
    * отчет по Начислениям 
    */
    public function actionAccrual()
    {
        /* всех кроме руководителей редиректим обратно */
        if(Yii::$app->session->get('user.ustatus')!=3) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        /* всех кроме руководителей редиректим обратно */
        
		/* объявляем переменные */
		$i = 0;
		$limit = 10;
		$offset = 0;
		$pages = NULL;
		$tid = NULL;
		$accruals= [];
		$groups = [];		
		$list = [];
		$listteachers = [];
		$teachers = [];		
		$teachers_list = [];
		
		/* задаем переменную для фильтрации результата по id преподавателя */
        $tid = self::getTeacherID();

        /* получаем список id преподавателей по которым есть занятия для начисления и начисления для выплаты */
        $listteachers = AccrualTeacher::getTeachersWithViewedLessonsIds();

        /* считаем количество преподавателей */
        $pages = count($listteachers);

        /* получаем список id и имен преподавателей по которым есть занятия для начисления и начисления для выплаты */
        $teachers_list = AccrualTeacher::getTeachersWithViewedLessonsList();

        /* высчитываем смещение относительно начала массива */
        if(Yii::$app->request->get('page')&&(int)Yii::$app->request->get('page')<=ceil($pages/$limit)){
            $offset = 10 * ((int)Yii::$app->request->get('page') - 1);
        }
        
        /* если преподаватель не задан */
        if(!$tid || $tid=='all') {
            /* вырезаем из массива 10 преподавателей с соответствующим смещением */
            $list = array_slice($listteachers, $offset, $limit);
        } else {
            $list[0] = $tid;
        }
        
        /* получаем список преподавателей с доп. информацией */
        $teachers = AccrualTeacher::getTeachersWithViewedLessonsInfo($list);
        
        /* получаем данные по занятиям ожидающим начисление */
        $order = ['jg.calc_teacher'=>SORT_ASC, 'jg.calc_groupteacher'=>SORT_DESC, 'jg.id'=>SORT_DESC];
        $lessons = AccrualTeacher::getViewedLessonList($list, $order);
        
        /* получаем список доступных начислений по преподавателям */
        $accruals = AccrualTeacher::getAccrualsByTeacherList($list);
        
        // создаем массив с данными по группам и суммарному колич часов
        foreach($lessons as $lesson){
            $groups[$lesson['gid']][$lesson['tid']]['tid'] = $lesson['tid'];
            $groups[$lesson['gid']][$lesson['tid']]['gid'] = $lesson['gid'];
            $groups[$lesson['gid']][$lesson['tid']]['tjplace'] = $lesson['tjplace'];
            $groups[$lesson['gid']][$lesson['tid']]['course'] = $lesson['service'];
            $groups[$lesson['gid']][$lesson['tid']]['level'] = $lesson['level'];
            $groups[$lesson['gid']][$lesson['tid']]['service'] = $lesson['sid'];
            $groups[$lesson['gid']][$lesson['tid']]['office'] = $lesson['office'];
            if(isset($groups[$lesson['gid']][$lesson['tid']]['time'])){
                $groups[$lesson['gid']][$lesson['tid']]['time'] += $lesson['time'];
            } else {
                $groups[$lesson['gid']][$lesson['tid']]['time'] = $lesson['time'];
            }
            $lessons[$i]['money'] = round(AccrualTeacher::getLessonFinalCost($teachers, $lesson), 2);
            $i++;
        }
        // получаем данные по посещаемости занятий для рассчета коэффициента
        
        /* выводим данные в представление */
        return $this->render('accrual',[
            'teachers' => $teachers,
            'lessons' => $lessons,
            'groups' => $groups,
            'pages' => $pages,
            'teachers_list' => $teachers_list,
            'tid' => $tid,
            'accruals' => $accruals,
            'reportlist' => Report::getReportTypeList(),
			'userInfoBlock' => User::getUserInfoBlock(),
			'jobPlace' => [ 1 => 'ШИЯ', 2 => 'СРР' ]
        ]);
        /* выводим данные в вьюз */
    }

    public function actionMargin()
    {
        /* всех кроме руководителей и бухгалтеров редиректим обратно */
        if(Yii::$app->session->get('user.ustatus')!=3) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        /* всех кроме руководителей и бухгалтеров редиректим обратно */

        if(Yii::$app->request->get('month')) {
            $month = Yii::$app->request->get('month');
        } else {
            $month = date('n');
        }
		
        if(Yii::$app->request->get('year')) {
            $year = Yii::$app->request->get('year');
        } else {
            $year = date('Y');
        }
		
		$teachers = (new \yii\db\Query())
        ->select('t.id as tid, t.name as teacher_name')
        ->distinct()
        ->from('calc_teacher t')
        ->innerJoin('calc_accrualteacher at', 't.id=at.calc_teacher')
		->innerJoin('calc_journalgroup jg', 'jg.calc_accrual=at.id and jg.calc_teacher=t.id')
        ->where('at.visible=:one and jg.visible=:one and t.visible=:one', [':one'=>1])
		->andFilterWhere(['MONTH(jg.data)' => $month])
		->andFilterWhere(['YEAR(jg.data)' => $year])
        ->orderby(['t.name' => SORT_ASC, 't.id' => SORT_ASC])
        ->all();
		
		if(!empty($teachers)) {
			$lessons = (new \yii\db\Query())
			->select('t.id as tid, COUNT(jg.id) as count')
			->from('calc_teacher t')
			->innerJoin('calc_accrualteacher at', 't.id=at.calc_teacher')
			->innerJoin('calc_journalgroup jg', 'jg.calc_accrual=at.id and jg.calc_teacher=t.id')
			->where('at.visible=:one and jg.visible=:one and t.visible=:one', [':one'=>1])
			->andFilterWhere(['MONTH(jg.data)' => $month])
			->andFilterWhere(['YEAR(jg.data)' => $year])
			->groupby(['t.id'])
			->all();
		
			$accruals = (new \yii\db\Query())
			->select('t.id as tid, at.id as aid, at.value as value')
			->distinct()
			->from('calc_teacher t')
			->innerJoin('calc_accrualteacher at', 't.id=at.calc_teacher')
			->innerJoin('calc_journalgroup jg', 'jg.calc_accrual=at.id and jg.calc_teacher=t.id')
			->where('at.visible=:one and jg.visible=:one and t.visible=:one', [':one'=>1])
			->andFilterWhere(['MONTH(jg.data)' => $month])
			->andFilterWhere(['YEAR(jg.data)' => $year])
			->all();
		
			$subQuery = (new \yii\db\Query())
			->select('COUNT(sjg.id)')
			->from('calc_studjournalgroup sjg')
			->where('sjg.calc_journalgroup=jg.id and sjg.calc_statusjournal!=:two');
		
			$income = (new \yii\db\Query())
			->select('t.id as tid, jg.id as jid, gt.calc_service as sid, jg.data as date')
			->addSelect(['count' => $subQuery])
			->from('calc_teacher t')
			->innerJoin('calc_accrualteacher at', 't.id=at.calc_teacher')
			->innerJoin('calc_journalgroup jg', 'jg.calc_accrual=at.id and jg.calc_teacher=t.id')
			->innerJoin('calc_groupteacher gt', 'jg.calc_groupteacher=gt.id')
			->where('at.visible=:one and jg.visible=:one and t.visible=:one', [':one' => 1, ':two' => 2])
			->andFilterWhere(['MONTH(jg.data)' => $month])
			->andFilterWhere(['YEAR(jg.data)' => $year])
			->all();
		
			if(!empty($income)) {
				$i = 0;
				foreach($income as $in) {
				$cost = 0;
				
				$servhist = (new \yii\db\Query())
				->select('date as date, value as value')
				->from('calc_servicehistory')
				->where('calc_service=:sid', [':sid'=>$in['sid']])
				->all();
				
				if(!empty($servhist)) {
					if(count($servhist) > 1) {
						foreach($servhist as $sh) {
							if($in['date'] < date('Y-m-d', strtotime($sh['date']))) {
								$cost = $sh['value'];
							} 
						}
					} else {
						if($in['date'] < date('Y-m-d', strtotime($servhist[0]['date']))) {
							$cost = $servhist[0]['value'];
						}
					}
				}
				
				if($cost == 0) {
					$lesson_cost = (new \yii\db\Query())
					->select('sn.value as value')
					->from('calc_service s')
					->leftJoin('calc_studnorm sn', 'sn.id=s.calc_studnorm')
					->where('s.id=:sid', [':sid'=>$in['sid']])
					->one();
					
					$cost = $lesson_cost['value'];
				}
				
				$income[$i]['cost'] = $cost;
				$i++;
				}
				unset($in);
			}
			
			$i = 0;
			foreach($teachers as $t) {
				foreach($lessons as $l) {
					if($l['tid'] == $t['tid']) {
						$teachers[$i]['lesson_count'] = $l['count'];
					}				
				}
				
				$teachers[$i]['sum_accrual'] = 0;
				foreach($accruals as $a) {
					if($a['tid'] == $t['tid']) {
						$teachers[$i]['sum_accrual'] += $a['value'];
					}
				}
				$teachers[$i]['sum_income'] = 0;
				foreach($income as $in) {
					if($in['tid'] == $t['tid']) {
						$teachers[$i]['sum_income'] += $in['count'] * $in['cost'];
					}
				}
				$i++;			
			}
		}
		
        /* выводим данные в вьюз */      
        return $this->render('margin',[
            'month' => $month,
            'year' => $year,
			'teachers' => $teachers,
            'reportlist' => Report::getReportTypeList(),
			'userInfoBlock' => User::getUserInfoBlock(),
        ]);
        /* выводим данные в вьюз */

    }

    // Отчет по оплатам
    public function actionPayments (string $start = NULL, string $end = NULL, string $oid = NULL)
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 &&
            (int)Yii::$app->session->get('user.ustatus') !== 4 &&
            (int)Yii::$app->session->get('user.ustatus') !== 8) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        if (!($start && $end)) {
            $start = date("Y-m-d", strtotime('monday this week'));
            $end = date("Y-m-d", strtotime('sunday this week'));
        }
        $oid = (int)Yii::$app->session->get('user.ustatus') === 4 ? (int)Yii::$app->session->get('user.uoffice_id') : $oid;
        $report = new Report();
        $payments = $report->getPayments($start, $end, $oid);
        return $this->render('payments', [
            'end'           => $end,
            'offices'       => Office::getOfficesListSimple(),
            'oid'           => $oid,
            'payments'      => $payments,
            'reportList'    => Report::getReportTypeList(),
            'start'         => $start,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    // Отчет по счетам
    public function actionInvoices(string $start = '', string $end = '', string $oid = '') {
        if((int)Yii::$app->session->get('user.ustatus') !== 3 &&
           (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        if (!($start && $end)) {
            $start = date("Y-m-d", strtotime('monday this week'));
            $end = date("Y-m-d", strtotime('sunday this week'));
        }
        $oid = (int)Yii::$app->session->get('user.ustatus') === 4 ? (int)Yii::$app->session->get('user.uoffice_id') : $oid;
        $invoice = new Invoicestud();
        $invoices = $invoice->getInvoices([
            'start'  => $start,
            'end'    => $end,
            'office' => $oid,
        ]);
        $dates = [];
        foreach ($invoices as $inv){
            $dates[] = $inv['date'];
        }
        if (!empty($dates)) {
            $dates = array_unique($dates);
        }
        return $this->render('invoices', [
            'dates'         => $dates,
            'end'           => $end,
            'invoices'      => $invoices,
            'offices'       => Office::getOfficesListSimple(),
            'oid'           => $oid,
            'reportList'    => Report::getReportTypeList(),
            'start'         => $start,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    public function actionPlan()
	{
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 8) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
		
		$month = date('n');
		$year = date('Y');
		$monthname = date('F');
		
		// проверяем передан ли id офиса в get
		if(Yii::$app->request->get('oid')) {
			$oid = Yii::$app->request->get('oid');
		} else {
			// если офис явно не указан, то задаем центральный
			$oid = 16;
		}
		// проверяем передан ли id офиса в get
		
		// проверяем передан ли параметр данных отчета (тек. или след. месяц)
		if(Yii::$app->request->get('next')) {
			$next = Yii::$app->request->get('next');
		} else {
			// если параметр явно не указан, то задаем null
			$next = NULL;
		}
		// проверяем передан ли параметр данных отчета (тек. или след. месяц)
		
		// находим информацию по офису
		$office = Office::findOne($oid);
		// находим информацию по офису
		
		// формируем субзапрос для получения колич учеников в группе
		$subQuery = (new \yii\db\Query())
		->select('COUNT(sg.calc_studname) as cnt')
		->from('calc_studgroup sg')
		->where('sg.calc_groupteacher=gt.id and sg.visible=:one', [':one' => 1]);
		// формируем субзапрос для получения колич учеников в группе
		
		// получаем данные для таблицы
		$schedule = (new \yii\db\Query())
		->select('gt.id as group, sn.value as cost, sch.calc_denned as day, COUNT(sch.id) as cnt')
		->addSelect(['pupils' => $subQuery])
		->from('calc_schedule sch')
		->leftJoin('calc_groupteacher gt', 'gt.id=sch.calc_groupteacher')
		->leftJoin('calc_service s', 's.id=gt.calc_service')
		->leftJoin('calc_studnorm sn', 'sn.id=s.calc_studnorm')
		->where('sch.visible=1 and gt.visible=1 and sch.calc_office=:oid', [':oid' => $oid])
		->groupby(['gt.id', 'sn.value', 'sch.calc_denned'])
		->all();
		// получаем данные для таблицы
		
		$i = 0;
		$lessonplan = 0;
		$moneyplan = 0;
		
		// если массив не пустой, считаем колич занятий и сумму по плану и дописываем в массив
		if(!empty($schedule)) {
			foreach($schedule as $s) {
				// если необходима инфармация по следующему месяцу, опрелеляем след месяц и год
				if($next) {
					$dt = new \DateTime(date('Y-m-d'));
					$dt->modify('next month');
					$month = $dt->format('n');
					$year = $dt->format('Y');
					$monthname = $dt->format('F');
				}
				// если необходима инфармация по следующему месяцу, опрелеляем след месяц и год
				
				// обращаемся к внешней функции для рассчета колич дней
				$totalcount = $s['cnt'] * $this->reportHowdays($s['day'], $month, $year);
				$lessonplan += $totalcount;
				$moneyplan += $totalcount * $s['cost'] * $s['pupils'];
				$schedule[$i]['totalcnt'] = $totalcount;
				$schedule[$i]['totalcost'] = $totalcount * $s['cost'];
				$i++;
			}
			unset($s);
		}
		// если массив не пустой, считаем колич занятий и сумму по плану и дописываем в массив
		
        /* выводим данные в вьюз */		
		return $this->render('plan',[
			'grouplist' => $schedule,
			'lessonplan' => $lessonplan,
			'moneyplan' => $moneyplan,
			'daynames' => Tool::getDayOfWeekSimple(),
			'oid' => $oid,
			'office' => $office,
			'offices' => Office::getOfficeInScheduleListSimple(),
			'monthname' => $monthname,
			'next' => $next,
            'reportlist' => Report::getReportTypeList(),
			'userInfoBlock' => User::getUserInfoBlock(),
		]);
		/* выводим данные в вьюз */
	}

    public function actionSalaries ($end = null, $limit = 10, $offset = 0, $tid = null, $start = null)
    {
        /* всех кроме руководителей и бухгалтеров редиректим обратно */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 8) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $salaries = Report::getSalariesReportRows([
                'end' => $end,
                'id' => $tid,
                'limit' => $limit,
                'offset' => $offset,
                'start' => $start,
            ]);
            return [
                'status' => true,
                'menuData' => Report::getReportTypes(),
                'salariesData' => [
                    'columns' => Report::getSalariesReportColumns(),
                    'rows' => $salaries['rows'],
                    'total' => $salaries['total']
                ]
            ];
        } else {
            return $this->render('salaries');
        }
    }

    public function actionSale()
    {
        /* всех кроме руководителей редиректим обратно */
        if(Yii::$app->session->get('user.ustatus')!=3) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        /* всех кроме руководителей редиректим обратно */

        $params = [
		    'page' => 1,
            'limit' => 20,
            'offset' => 0,
            'page_count' => 0,
            'el_count' => 0
        ];
		
		$params['page_count'] = ceil(Sale::getAssignedSaleCount()/$params['limit']);

        /* высчитываем смещение относительно начала массива */
        if(Yii::$app->request->get('page') && (int)Yii::$app->request->get('page') > 1  && (int)Yii::$app->request->get('page') <= $params['page_count']){
            $params['offset'] = $params['limit'] * ((int)Yii::$app->request->get('page') - 1);
            $params['page'] = (int)Yii::$app->request->get('page');
        }
        
        $sales = Sale::getAssignedSaleList($params);
        $clients = [];

        if(!empty($sales)) {

            $params['el_count'] = count($sales);

	        $sale_ids = [];        
	        foreach($sales as $s) {
	            $sale_ids[] = $s['sale_id'];
	        }
        
            $clients = Sale::getClientListById($sale_ids);
        
        }

        /* выводим данные в вьюз */		
		return $this->render('sale',[
		    'params' => $params,
            'sales' => $sales,
            'clients' => $clients,
            'reportlist' => Report::getReportTypeList(),
			'userInfoBlock' => User::getUserInfoBlock(),
		]);
		/* выводим данные в вьюз */
    }
    
    /**
     * Общий отчет
     */ 

    public function actionCommon()
    {
        /* всех кроме бухгалтера и руководителей редиректим туда откуда пришли */
        if(Yii::$app->session->get('user.ustatus')!=3 && Yii::$app->session->get('user.ustatus')!=8) {            
            return $this->redirect(Yii::$app->request->referrer);
        }
        /* всех кроме бухгалтера и руководителей редиректим туда откуда пришли */

        /* проверяем get-запрос на наличие информации о годе и задаем переменную $year */
        if(Yii::$app->request->get('year')){
            $year = Yii::$app->request->get('year');
        } else {
            $year = date('Y');
        }
        /* проверяем get-запрос на наличие информации о годе и задаем переменную $year */
        
        /* проверяем get-запрос на наличие информации о месяце и задаем переменную $mon */
        if(Yii::$app->request->get('month')) {
            if(Yii::$app->request->get('month')>=1 && Yii::$app->request->get('month')<=12) {
                $month = Yii::$app->request->get('month');
            } else {
                $month = NULL;
            }
        } else {
            $month = NULL;
        }
        /* проверяем get-запрос на наличие информации о месяце и задаем переменную $mon */
        
        /* проверяем get-запрос на наличие информации о неделе и задаем переменные $week, $first_day, $last_day */
        if(Yii::$app->request->get('week')){
            if(Yii::$app->request->get('week')!='all') {
                $week = Yii::$app->request->get('week');
                $first_day = date('Y-m-d', $week * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400 + 86400);
                $last_day = date('Y-m-d', ($week + 1) * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400);
            } else {
                $week = NULL;
                $first_day = NULL;
                $last_day = NULL;
            }
        } else {
            $weekinfo = Report::getWeekInfo(date('j'), date('m'), date('Y'));
            $week = $weekinfo['num'];          
            $first_day = date('Y-m-d', $weekinfo['start']);
            $last_day = date('Y-m-d', $weekinfo['end']);
        }
        /* проверяем get-запрос на наличие информации о неделе и задаем переменные $week, $first_day, $last_day */

        /* выбираем список месяцев */
        $arr_months = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_month')
        ->where('visible=:vis', [':vis' => 1])
        ->all();
        
        $months = [];
        foreach($arr_months as $m){
            $months[$m['id']] = $m['name'];
        }
        unset($m);
        unset($arr_months);
        /* выбираем список месяцев */
        
        /* задаем пустые переменные */
        $common_report = [];
        /* задаем пустые переменные */

        /* определяем условия выборки данных для отчета */
        if($month && $year) {
            $fd = NULL;
            $ld = NULL;
            $m = $month;
            $y = $year;
        } elseif(!$first_day && !$last_day && !$month && $year) {
            $fd = NULL;
            $ld = NULL;
            $m = NULL;
            $y = $year;
        } else {
            $fd = $first_day;
            $ld = $last_day;
            $m = NULL;
            $y = NULL;
        }
        /* определяем условия выборки данных для отчета */
            
        /* получаем список офисов */
        $offices = (new \yii\db\Query())
        ->select('id as oid, name as oname')
        ->from('calc_office')
        ->where('visible=1')
        ->andWhere(['not in','id',['20','17','15','14','13']])
        ->all();
        /* получаем список офисов */
            
        /* получаем оплаты */
        $common_payments = (new \yii\db\Query())
        ->select('ms.calc_office as oid, SUM(value_card) as card, SUM(value_cash) as cash, SUM(value_bank) as bank, SUM(ms.value) as money')
        ->from('calc_moneystud ms')
        ->where('ms.visible=1 and ms.remain=0')
        ->andFilterWhere(['>=', 'ms.data', $fd])
        ->andFilterWhere(['<=', 'ms.data', $ld])
        ->andFilterWhere(['MONTH(ms.data)'=>$m])
        ->andFilterWhere(['YEAR(ms.data)'=>$y])
        ->groupby(['ms.calc_office'])
        ->all();
        /* получаем оплаты */
            
        /* получаем счета */
        $common_invoices = (new \yii\db\Query())
        ->select('is.calc_office as oid, SUM(is.value) as money, SUM(is.value_discount) as discount')
        ->from('calc_invoicestud is')
        ->where('is.visible=1')
        ->andFilterWhere(['>=', 'is.data', $fd])
        ->andFilterWhere(['<=', 'is.data', $ld])
        ->andFilterWhere(['MONTH(is.data)'=>$m])
        ->andFilterWhere(['YEAR(is.data)'=>$y])
        ->groupby(['is.calc_office'])
        ->all();           
        /* получаем счета */
            
        /* получаем начисления */
        $common_accruals = (new \yii\db\Query())
        ->select('gt.calc_office as oid, SUM(at.value) as money')
        ->from('calc_accrualteacher at')
        ->leftjoin('calc_groupteacher gt', 'gt.id=at.calc_groupteacher')
        ->andFilterWhere(['>=', 'at.data', $fd])
        ->andFilterWhere(['<=', 'at.data', $ld])
        ->andFilterWhere(['MONTH(at.data)'=>$m])
        ->andFilterWhere(['YEAR(at.data)'=>$y])
        ->groupby(['gt.calc_office'])
        ->all();
        /* получаем начисления */
            
        /* получаем часы */
        $common_hours = (new \yii\db\Query())
        ->select('gt.calc_office as oid, SUM(tn.value) as hours')
        ->from('calc_journalgroup jg')
        ->leftjoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
        ->leftjoin('calc_service s', 's.id=gt.calc_service')
        ->leftjoin('calc_timenorm tn', 'tn.id=s.calc_timenorm')
        ->where('jg.visible=1')
        ->andFilterWhere(['>=', 'jg.data', $fd])
        ->andFilterWhere(['<=', 'jg.data', $ld])
        ->andFilterWhere(['MONTH(jg.data)'=>$m])
        ->andFilterWhere(['YEAR(jg.data)'=>$y])
        ->groupby(['gt.calc_office'])
        ->all();
        /* получаем часы */

        /* получаем количество студентов */
        $subQuery = (new \yii\db\Query())
        ->select('count(DISTINCT sjg.calc_studname) as students')
        ->from('calc_studjournalgroup sjg')
        ->leftJoin('calc_journalgroup jg', 'jg.id=sjg.calc_journalgroup')
        ->leftJoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
        ->where('gt.calc_office=o.id and jg.view=:vis and sjg.calc_statusjournal=:vis', [':vis'=>1])
        ->andFilterWhere(['>=', 'jg.data', $fd])
        ->andFilterWhere(['<=', 'jg.data', $ld])
        ->andFilterWhere(['MONTH(jg.data)'=>$m])
        ->andFilterWhere(['YEAR(jg.data)'=>$y]);
        
        $common_students = (new \yii\db\Query())
        ->select('o.id as oid')
        ->addSelect(['students'=>$subQuery])
        ->from('calc_office o')
        ->where('o.visible=:vis', [':vis'=>1])
        ->andWhere(['not in','o.id',['20','17','15','14','13']])
        ->all();
        /* получаем количество студентов */

        /* получаем долги */
        $common_debts = [];
        $i = 0;
        foreach($offices as $o) {
            $tmp_debts = (new \yii\db\Query())
            ->select('s.debt as debts')
            ->from('calc_studname s')
            ->leftjoin('calc_studgroup sg', 's.id=sg.calc_studname')
            ->leftjoin('calc_groupteacher gt', 'gt.id=sg.calc_groupteacher')
            ->where('sg.visible=:vis and s.debt<=:minus and gt.calc_office=:oid', [':vis'=>1, ':minus'=>0, ':oid'=>$o['oid']])
            ->groupby(['s.id'])
            ->all();
            if($o['oid']!=6) {
                $tmp = 0.001;
                foreach($tmp_debts as $td) {
                    $tmp += $td['debts'];
                }
                unset($td);
                $common_debts[$i]['oid'] = $o['oid'];
                $common_debts[$i]['debts'] = $tmp;
            }
            $i++;
        }
        unset($o);
        unset($tmp_debts);

        /* задаем начальные переменные для формирования многомерного массива с данными для таблицы */
        $i = 0;
        /* оплаты */
        $pmnts = ['cash' => 0, 'card' => 0, 'bank' => 0, 'money' => 0];
        /* счета */
        $nvcs = 0;
        /* скидки */
        $dscnt = 0;
        /* начисления */
        $ccrls = 0;
        /* часы */
        $hrs = 0;
        /* долги */
        $dbts = 0;
        /* долги */
        $sts = 0;
        /* задаем начальные переменные для формирования многомерного массива с данными для таблицы */
            
        /* формируем основной массив с данными для отчета */
        foreach($offices as $o) {
            // создаем вложенный массив и задаем id офиса
            $common_report[$i]['oid'] = $o['oid'];
            // задаем имя офиса
            $common_report[$i]['name'] = $o['oname'];
            // задаем дефолтное значение оплат по офису
            $common_report[$i]['payments'] = [];
            // задаем дефолтное значение счетов по офису
            $common_report[$i]['invoices'] = 0;
            // задаем дефолтное значение скидок по офису
            $common_report[$i]['discounts'] = 0;
            // задаем дефолтное значение начислений по офису
            $common_report[$i]['accruals'] = 0;
            // задаем дефолтное значение часов по офису
            $common_report[$i]['hours'] = 0;
            // задаем дефолтное значение долгов по офису
            $common_report[$i]['debts'] = 0;
            // распечатываем массив с оплатами
            foreach($common_payments as $pay) {
                // выбираем оплаты по id офиса
                if($common_report[$i]['oid'] == $pay['oid']) {
                    // вносим сумму оплат по офису в массив
                    $common_report[$i]['payments'] = ['cash' => $pay['cash'], 'card' => $pay['card'], 'bank' => $pay['bank'], 'money' => $pay['money']];
                    // суммируем оплаты для поледущего получения итогового значения
                    $pmnts['cash'] += $pay['cash'];
                    $pmnts['card'] += $pay['card'];
                    $pmnts['bank'] += $pay['bank'];
                    $pmnts['money'] += $pay['money'];
                }
            }
            // распечатываем массив со счетами
            foreach($common_invoices as $inv) {
                // выбираем счета по id офиса
                if($common_report[$i]['oid'] == $inv['oid']) {
                    // вносим сумму счетов по офису в массив
                    $common_report[$i]['invoices'] = $inv['money'];
                    // вносим сумму скидок счетов по офису в массив
                    $common_report[$i]['discounts'] = $inv['discount'];
                    // суммируем счета для последующего получения итогового значения
                    $nvcs = $nvcs + $inv['money'];
                    // суммируем скидки для последующего получения итогового значения 
                    $dscnt = $dscnt + $inv['discount'];
                }
            }
            // распечатываем массив с начислениями
            foreach($common_accruals as $acr) {
                // выбираем начисления по id офиса
                if($common_report[$i]['oid'] == $acr['oid']) {
                    // вносим сумму начислений по офису в массив
                    $common_report[$i]['accruals'] = $acr['money'];
                    // суммируем начисления для поледущего получения итогового значения
                    $ccrls = $ccrls + $acr['money'];
                }
            }
            // распечатываем массив с часами
            foreach($common_hours as $hr) {
                // выбираем часы по id офиса
                if($common_report[$i]['oid'] == $hr['oid']) {
                    // вносим сумму часов по офису в массив
                    $common_report[$i]['hours'] = $hr['hours'];
                    // суммируем часы для последущего получения итогового значения
                    $hrs = $hrs + $hr['hours'];
                }
            }
            // распечатываем массив со студентами
            foreach($common_students as $st) {
                // выбираем студентов по id офиса
                if($common_report[$i]['oid'] == $st['oid']) {
                    // вносим сумму студентов по офису в массив
                    $common_report[$i]['students'] = $st['students'];
                    // суммируем студентов для последущего получения итогового значения
                    $sts = $sts + $st['students'];
                }
            }
            // распечатываем массив с долгами
            foreach($common_debts as $db) {
                // выбираем часы по id офиса
                if($common_report[$i]['oid'] == $db['oid']) {
                    // вносим сумму часов по офису в массив
                    $common_report[$i]['debts'] = $db['debts'];
                    // суммируем долги для последущего получения итогового значения
                    $dbts = $dbts + $db['debts'];
                }
            }
            // увеличиваем номер
            $i++;  
        }
        /* формируем основной массив с данными для отчета */

        /* ставим число побольше чтобы точно не совпадало с id офисов */
        $i = 999;
        /* добавляем последний вложенный массив в котором будут итоговые суммарные значения по столбцам
        *  задаем id офиса - в данном случае совпадает с номером массива
        */
        $common_report[$i]['oid'] = $i;
        /* задаем имя массива */
        $common_report[$i]['name'] = 'Итого:';
        /* задаем итоговую сумму по оплатам */
        $common_report[999]['payments'] = $pmnts;
        /* задаем итоговвую сумму по счетам */
        $common_report[999]['invoices'] = $nvcs;
        /* задаем итоговую сумму по скидкам */
        $common_report[999]['discounts'] = $dscnt;
        /* задаем итоговую сумму по начислениям */
        $common_report[999]['accruals'] = $ccrls;
        /* задаем итоговую сумму по часам */
        $common_report[999]['hours'] = $hrs;
        /* задаем итоговую сумму по студентам */
        $common_report[999]['students'] = $sts;
        /* задаем итоговую сумму по долгам */
        $common_report[999]['debts'] = $dbts;

        return $this->render('common',[
            'months' => $months,
            'common_report' => $common_report,
            'year' => $year,
            'month' => $month,
            'week' => $week,
            'weeks' => Report::getWeekList($year),
            'reportlist' => Report::getReportTypeList(),
			'userInfoBlock' => User::getUserInfoBlock(),
        ]);

    }

    public function actionDebt()
	{
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {            
            return $this->redirect(Yii::$app->request->referrer);
        }
        $req   = Yii::$app->request;
        $oid   = (int)Yii::$app->session->get('user.ustatus') === 4 ? Yii::$app->session->get('user.uoffice_id') : $req->get('OID', NULL);;
        $page  = $req->get('page', NULL);
        $sign  = $req->get('SIGN', '0');
        $state = $req->get('STATE', '1');
        $tss   = $req->get('TSS', NULL);

        // запрашиваем список студентов
        $stds = (new \yii\db\Query())
        ->select(['id' => 'sn.id', 'name' => 'sn.name', 'debt' => 'sn.debt'])
        ->from(['sn' => Student::tableName()]);
        if ($oid) {
            $stds = $stds->innerJoin(['so' => 'student_office'], 'sn.id = so.student_id');
        }
        $stds = $stds
        ->where([
            'sn.visible' => 1
        ])
        ->andFilterWhere([
            $sign === '1' ? '>' : '<',
            'sn.debt',
            $sign === '' ? NULL: 0
        ])
        ->andFilterWhere(['sn.active'    => $state === '' ? NULL : $state])
        ->andFilterWhere(['so.office_id' => $oid ===   '' ? NULL : $oid])
        ->andFilterWhere(['like', 'sn.name', $tss]);
            
        // делаем клон запроса
        $countQuery = clone $stds;
        // получаем данные для паджинации
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        // задаем параметры для паджинации
        $limit = 20;
        $offset = 0;
        if ($page > 1 && $page <= $pages->totalCount) {
            $offset = 20 * ($page - 1);
        }
        
        // доделываем запрос и выполняем
        $stds = $stds->orderby(['sn.name'=>SORT_ASC])->limit($limit)->offset($offset)->all();
        $i = 0;
        $stids = [];
        // формируем массив для последующей группировки услуг по студенту
        foreach($stds as $s) {
            //$tmp_stdnts[$s['stid']] = $s['stname']; 
            $stids[$i] = $s['id'];
            $i++;
        }
        $students = [];
        if(!empty($stids)) {
            // запрашиваем услуги назначенные студенту
            $students = (new \yii\db\Query())
            ->select('s.id as sid, s.name as sname, is.calc_studname as stid, SUM(is.num) as num')
            ->distinct()
            ->from('calc_service s')
            ->leftjoin(['is' => Invoicestud::tableName()], 'is.calc_service = s.id')
            ->where([
                'is.remain' => [Invoicestud::TYPE_NORMAL, Invoicestud::TYPE_NETTING],
                'is.visible' => 1
            ])               
            ->andWhere(['in', 'is.calc_studname', $stids])
            ->groupby(['is.calc_studname', 's.id'])
            ->orderby(['s.id' => SORT_ASC])
            ->all();
            // запрашиваем услуги назначенные студенту
            
            // проверяем что у студента есть назначенные услуги
            if(!empty($students)){
                $i = 0;
                // распечатываем массив
                foreach($students as $service){
                    $schedule = new Schedule();
                    $studentSchedule = $schedule->getStudentSchedule($service['stid']);
                    // запрашиваем из базы колич пройденных уроков
                    $lssns = (new \yii\db\Query())
                    ->select('COUNT(sjg.id) AS cnt')
                    ->from('calc_studjournalgroup sjg')
                    ->leftjoin('calc_groupteacher gt', 'sjg.calc_groupteacher=gt.id')
                    ->leftjoin('calc_journalgroup jg', 'sjg.calc_journalgroup=jg.id')
                    ->where('jg.view=:vis and jg.visible=:vis and (sjg.calc_statusjournal=:vis or sjg.calc_statusjournal=:stat) and gt.calc_service=:sid and sjg.calc_studname=:stid', [':vis'=>1, 'stat'=>3, ':sid'=>$service['sid'], ':stid'=>$service['stid']])
                    ->one();
                    // считаем остаток уроков
                    $cnt = $students[$i]['num'] - $lssns['cnt'];
                    $students[$i]['num'] = $cnt;
                    $students[$i]['npd'] = Moneystud::getNextPaymentDay($studentSchedule, $service['sid'], $cnt);
                    $i++;
                }                 
            }
            // проверяем что у студента есть назначенные услуги
        }

        return $this->render('debt',[
            'offices'       => Office::getOfficeInScheduleListSimple(),
            'oid'           => $oid,
            'pages'         => $pages,
            'reportlist'    => Report::getReportTypeList(),
            'sign'          => $sign,
            'state'         => $state,
            'stds'          => $stds,
            'students'      => $students,
            'tss'           => $tss,
			'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    public function actionLessons()
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4 && (int)Yii::$app->session->get('user.ustatus') !== 6) {            
            return $this->redirect(Yii::$app->request->referrer);
        }
        $searchModel = new LessonSearch();
        return $this->render('lessons', [
            'dataProvider'  => $searchModel->search(Yii::$app->request->queryParams),
            'reportlist'    => Report::getReportTypeList(),
            'searchModel'   => $searchModel,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    public function actionIndex()
    {
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 8) {
            return $this->redirect(['report/common']);
        } else if((int)Yii::$app->session->get('user.ustatus') === 4) {
            return $this->redirect(['report/journals']);
        } else if((int)Yii::$app->session->get('user.ustatus') === 6) {
            return $this->redirect(['report/lessons']);
        } else if ((int)Yii::$app->session->get('user.uid') === 296) {
            return $this->redirect(['report/teacher-hours']);
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
    
    /*
    * метод выборки данных для построения отчета по Журналам 
    */
    
    public function actionJournals($corp = 0, $oid = NULL, $tid = NULL, $page = NULL) 
    {
        /* всех кроме руководителей, менеджеров редиректим обратно */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        // для менеджеров задаем переменную с id офиса
        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
            $oid = $oid ?? Yii::$app->session->get('user.uoffice_id');
            if ($corp) {
                $oid = NULL;
            }
        }
        // получим массив преподавателей у которых его активные группы
        $teachers = (new \yii\db\Query())
        ->select('t.id as tid, t.name as tname')
        ->distinct()
        ->from('calc_teachergroup tg')
        ->innerJoin('calc_teacher t', 't.id=tg.calc_teacher')
        ->innerJoin('calc_groupteacher gt', 'gt.id=tg.calc_groupteacher')
        ->where([
            'gt.visible' => 1,
            'tg.visible' => 1
        ])
        ->andFilterWhere(['t.id' => $tid ?? NULL])
        ->andFilterWhere(['gt.calc_office' => $oid ?? NULL])
        ->andFilterWhere(['gt.corp' => $corp ?? NULL]);
        // делаем клон запроса
        $countQuery = clone $teachers;
        // получаем данные для паджинации
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $limit = 10;
        $offset = 0;
        if ($page) {
            if ($page > 1 && $page <= $pages->totalCount) {
                $offset = 10 * ($page - 1);
            }
        }
        // доделываем запрос и выполняем
        $teachers = $teachers->orderBy(['t.name'=>SORT_ASC])->limit($limit)->offset($offset)->all();            
        $teachersall = $countQuery->orderBy(['t.name'=>SORT_ASC])->all();

        // зададим пустое значение, оно будет использоваться если фильтр по преподавателю не задан
        $tids = NULL;
        $teacher_names = NULL;
        $lcount = [];
        // формируем массив с id преподавателей, для ситуации когда фильтр по преподавателю не задан
        if (!$tid) {
            $i = 0;
            foreach($teachers as $t) {
                // массив id-шников для запроса занятий
                $tids[$i] = $t['tid'];
                // массив преподавателей для вьюза
                $teacher_names[$t['tid']] = $t['tname'];
                // массив занятий преподавателя
                $lcount[$t['tid']]['totalCount'] = 0;
                $i++;
            }
        } else {
            foreach($teachersall as $t) {
                if($t['tid']==$tid) {
                    // массив преподавателей для вьюза
                    $teacher_names[$t['tid']] = $t['tname'];
                    // массив занятий преподавателя
                    $lcount[$t['tid']]['totalCount'] = 0;
                }
            }
        }

        if(!empty($teacher_names)) {
            // получаем данные по занятиям
            $lessons = (new \yii\db\Query())
            ->select('jg.id as lid, jg.calc_groupteacher as gid, jg.data as date, jg.done as done, jg.calc_teacher as tid, t.name as tname, jg.description as desc, jg.visible as visible')
            ->from('calc_journalgroup jg')
            ->leftJoin('calc_teacher t', 't.id=jg.calc_teacher')
            ->leftJoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
            ->where([
                'jg.view' => 0,
                'jg.visible' => 1
            ])
            ->andWhere(['>', 'jg.user', 0])
            ->andFilterWhere(['gt.calc_office' => $oid ?? NULL])
            ->andFilterWhere(['jg.calc_teacher' => $tid ?? NULL])
            ->andFilterWhere(['in', 'jg.calc_teacher', $tids])
            ->andFilterWhere(['gt.corp' => $corp ?? NULL])
            ->orderby(['t.name' => SORT_ASC, 'jg.data' => SORT_DESC])
            ->all();

            // выбираем группы преподавателей
            $groups = (new \yii\db\Query())
            ->select('tg.calc_groupteacher as gid, tg.calc_teacher as tid, s.id as sid, s.name as service, el.name as ename, tn.value as hours')
            ->from('calc_teachergroup tg')
            ->leftJoin('calc_groupteacher gt', 'gt.id=tg.calc_groupteacher')
            ->leftJoin('calc_service s', 's.id=gt.calc_service')
            ->leftJoin('calc_timenorm tn', 'tn.id=s.calc_timenorm')
            ->leftJoin('calc_edulevel el', 'el.id=gt.calc_edulevel')
            ->where(['gt.visible' => 1])
            ->andFilterWhere(['gt.calc_office' => $oid ?? NULL])
            ->andFilterWhere(['tg.calc_teacher' => $tid ?? NULL])
            ->andFilterWhere(['in', 'tg.calc_teacher', $tids])
            ->andFilterWhere(['gt.corp' => $corp ?? NULL])
            ->orderby(['tg.id' => SORT_ASC])
            ->all();
            
            foreach($groups as $g) {
                $t = 0;
                foreach($lessons as $l){
                    if($l['tid']==$g['tid']&&$l['gid']==$g['gid']) {
                        $t++;
                    }
                }
                unset($l);
                $lcount[$g['tid']][$g['gid']]['totalCount'] = $t;
                $lcount[$g['tid']]['totalCount'] += $t;
            }
        } else {
            $lessons = [];
            $groups = [];
        }
        $teachers = $teachersall;

        return $this->render('journals', [
            'corp'          => $corp,
            'groups'        => $groups,
            'lcount'        => $lcount,
            'lessons'       => $lessons,
            'offices'       => Office::getOfficeInScheduleListSimple(),
            'oid'           => $oid,
            'pages'         => $pages,
            'reportlist'    => Report::getReportTypeList(),
            'teachers'      => Teacher::getTeachersInUserListSimple(),
            'teacher_names' => $teacher_names,
            'tid'           => $tid,
			'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    // Отчет по оплатам
    public function actionTeacherHours()
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 &&
            (int)Yii::$app->session->get('user.ustatus') !== 4 &&
            (int)Yii::$app->session->get('user.ustatus') !== 6 &&
            (int)Yii::$app->session->get('user.uid') !== 296) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        $req    = Yii::$app->request;
        $start  = $req->get('start',  NULL);
        $end    = $req->get('end',    NULL);
        $tid    = $req->get('tid',    NULL);
        $limit  = $req->get('limit',  NULL);
        $offset = $req->get('offset', NULL);

        if (!($start && $end)) {
            $start = date("Y-m-d", strtotime('monday last week'));
            $end = date("Y-m-d", strtotime('sunday last week'));
        }
        $report = new Report();
        $result = $report->getTeacherHours([
            'end'    => $end    ? $end    : NULL,
            'start'  => $start  ? $start  : NULL,
            'tid'    => $tid    ? $tid    : NULL,
            'limit'  => $limit  ? $limit  : 10,
            'offset' => $offset ? $offset : 0,
        ]);
        return $this->render('teacher-hours', [
            'end'           => $end,
            'hours'         => $result['hours'],
            'pager'         => [
                'limit'  => $limit  ? $limit  : Report::DEFAULT_LIMIT,
                'offset' => $offset ? $offset : Report::DEFAULT_OFFSET,
                'total'  => $result['count'],
            ],
            'reportList'    => Report::getReportTypeList(),
            'start'         => $start,
            'teachers'      => Teacher::getTeachersInUserListSimple(),
            'tid'           => $tid,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }
    
    /*
    * метод выборки данных для построения отчета по Начислениям 
    */
    protected function reportAccruals($tid)
    {
        //получаем список преподавателей у которых есть занятия к начислению
        $tmpteachers = (new \yii\db\Query()) 
        ->select('jg.calc_teacher as id, t.name as name')
        ->from('calc_journalgroup jg')
        ->leftJoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
        ->leftJoin('calc_service s', 's.id=gt.calc_service')
        ->leftJoin('calc_timenorm tn', 'tn.id=s.calc_timenorm')
        ->leftJoin('calc_edulevel el', 'el.id=gt.calc_edulevel')
        ->leftJoin('calc_office o', 'o.id=gt.calc_office')
        ->leftJoin('calc_teacher t', 't.id=jg.calc_teacher')
        ->where('jg.done=0 and jg.view=1 and jg.visible=1')
        ->orderby(['t.name'=>SORT_ASC])->all();
        // перепечатываем id преподавателей в новый массив
        $i = 0;
        foreach($tmpteachers as $tmpteacher){
            $steachers[$i]=$tmpteacher['id'];
            $i++;
        }
        // оставляем только уникальные id
        $listteachers = array_unique($steachers);
        // считаем количество преподавателей
        $pages = count($listteachers);
    
        // перепечатываем id и имена преподавателей в новый массив
        foreach($tmpteachers as $tmpteacher){
            $selteachers[$tmpteacher['id']] = $tmpteacher['name'];
        }
        // оставляем только уникальные id
        $tchrs = array_unique($selteachers);

        // задаем лимит на выдачу преподавателей
        $limit = 10;
        // высчитываем смещение относительно начала массива
        if(Yii::$app->request->get('page')&&(int)Yii::$app->request->get('page')<=ceil($pages/$limit)){
            $offset = 10 * ((int)Yii::$app->request->get('page') - 1);
        } else {
            $offset = 0;
        }
        if(!$tid || $tid=='all') {
            // вырезаем из массива 10 преподавателей с соответствующим смещением
            $list = array_slice($listteachers, $offset, $limit);
        } else {
            $list[0] = $tid;
        }
        // получаем список преподавателей
        $teachers = (new \yii\db\Query()) 
        ->select('t.id as id, t.name as name, t.calc_statusjob as stjob, t.value_corp as vcorp, en.value as norm')
        ->from('calc_teacher t')
        ->leftJoin('calc_edunormteacher as ent', 'ent.calc_teacher=t.id')
        ->leftJoin('calc_edunorm en', 'en.id=ent.calc_edunorm')
        ->where('ent.active=1');
        if(!$tid || $tid=='all'){
            $teachers = $teachers->andWhere(['in','t.id',$list]);
        }
        else{
            $teachers = $teachers->andWhere('t.id=:tid',[':tid' => $tid]);
        }   
        $teachers = $teachers->orderby(['t.name'=>SORT_ASC])->all();

        // формируем подзапрос для выборки количество учеников на занятии
        $SubQuery = (new \yii\db\Query())
        ->select('count(sjg.id) as pupil')
        ->from('calc_studjournalgroup sjg')
        ->where('sjg.calc_journalgroup=jg.id and sjg.calc_statusjournal!=2');
        
        // получаем данные по занятиям ожидающим начисление
        $lessons = (new \yii\db\Query()) 
        ->select('jg.id as jid, jg.data as jdate, jg.calc_groupteacher as gid, s.id as sid, s.name as service, tn.value as time, jg.calc_teacher as tid, el.name as level, o.name as office, jg.description as desc, jg.calc_edutime as edutime, jg.view as view, gt.corp as corp')
        ->addSelect(['pcount'=>$SubQuery])
        ->from('calc_journalgroup jg')
        ->leftJoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
        ->leftJoin('calc_service s', 's.id=gt.calc_service')
        ->leftJoin('calc_timenorm tn', 'tn.id=s.calc_timenorm')
        ->leftJoin('calc_edulevel el', 'el.id=gt.calc_edulevel')
        ->leftJoin('calc_office o', 'o.id=gt.calc_office')
        ->where('jg.done=0 and jg.view=1 and jg.visible=1')
        ->andWhere(['in','jg.calc_teacher',$list])
        ->orderby(['jg.calc_teacher'=>SORT_ASC, 'jg.calc_groupteacher'=>SORT_DESC, 'jg.id'=>SORT_DESC])
        ->all();
        $i = 0;
        // создаем массив с данными по группам и суммарному колич часов
        foreach($lessons as $lesson){
            $groups[$lesson['gid']]['tid'] = $lesson['tid'];
            $groups[$lesson['gid']]['gid'] = $lesson['gid'];
            $groups[$lesson['gid']]['course'] = $lesson['service'];
            $groups[$lesson['gid']]['level'] = $lesson['level'];
            $groups[$lesson['gid']]['service'] = $lesson['sid'];
            $groups[$lesson['gid']]['office'] = $lesson['office'];
            if(isset($groups[$lesson['gid']]['time'])){
                $groups[$lesson['gid']]['time'] += $lesson['time'];
            } else {
                $groups[$lesson['gid']]['time'] = $lesson['time'];
            }
            /*
            $pupilscount = (new \yii\db\Query()) 
            ->select('count(sjg.calc_statusjournal) as num')
            ->from('calc_journalgroup jg')
            ->leftJoin('calc_studjournalgroup sjg', 'sjg.calc_journalgroup=jg.id')
            ->where('jg.done=:zero and jg.view=:vis and jg.visible=:vis and sjg.calc_statusjournal=:vis', [':vis'=>1, ':zero'=>0])
            ->andWhere(['jg.id'=>$lesson['jid']])
            ->andWhere(['jg.calc_teacher'=>$lesson['tid']])
            ->andWhere(['jg.calc_groupteacher'=>$lesson['gid']])
            ->one();
            */
            //$lessons[$i]['pcount'] = $lesson['pupil'];
            $accrual = 0;
            foreach($teachers as $t) {
                if($t['id']==$lesson['tid']) {
                    // задаем коэффициэнт по умолчанию
                    $koef = 1;                  
                    //выбираем коэффициент в зависимости от количества учеников
                    switch($lesson['pcount']) {
                        case 1: $koef = 1; break;
                        case 2: $koef = 1; break;
                        case 3: $koef = 1; break;
                        case 4: $koef = 1.1; break;
                        case 5: $koef = 1.2; break;
                        case 6: $koef = 1.3; break;
                        case 7: $koef = 1.4; break;
                        case 8: $koef = 1.5; break;
                        case 9: $koef = 1.6; break;
                        case 10: $koef = 1.7; break;
                    }
                    if($lesson['pcount'] > 10) {
                        $koef = 1.8;
                    }
            // задаем полную ставку (ставка + надбавка)
                    $fullnorm = $t['norm'];
                    // если надбавка больше 0
                    if($lesson['corp'] > 0) {
                        // суммируем ее со ставкой
                        $fullnorm = $t['norm'] + $t['vcorp'];
                    }               
                    // считаем сумму начисления
                    switch($lesson['edutime']){
                        // дневное время у всех из ставки вычитается 50 рублей
                        case 1: $accrual += ($fullnorm - 50) * $lesson['time'] * $koef; break;
                        // вечернее время используем полную ставку
                        case 2: $accrual += $fullnorm * $lesson['time'] * $koef; break;
                        // полурабочее время (с сентября 2016 не используется)
                        case 3: $accrual += $fullnorm * $lesson['time'] * (2/3) * $koef; break;
                    }
                }
            }
            $lessons[$i]['money'] = round($accrual, 2);
            $i++;
        }
        
        // получаем данные по посещаемости занятий длоя рассчета коэффициента


        return array($teachers, $lessons, $groups, $pages, $tchrs);
    }
    
	protected function reportHowdays($day, $month, $year)
	{
		// Считаем сколько дней в месяце
		$mdays = date("t", mktime(0, 0, 0, $month, 1, $year));
		$howday = 0; 
		   for ($i = 1; $i <= $mdays; $i++){ 
			// Цикл проходит по всем дням месяца. И если  условие верно то добавляем +1		   
			$wday  =  date("N", mktime(0, 0, 0, $month, $i, $year));  
				if ($wday == $day) {
					$howday++;
				}			
			 }
		return $howday;
    }
 /*  
    public function actionTemp() 
	{
        $services = (new \yii\db\Query())
        ->select('calc_service as sid, student_count as cnt, creation_date as date, id as id')
        ->from('calc_report_margin')
        ->where('MONTH(creation_date)=10')
        ->all();

        foreach($services as $s) {
            $cost = 0;
            
            $servhist = (new \yii\db\Query())
            ->select('date as date, value as value')
            ->from('calc_servicehistory')
            ->where('calc_service=:sid', [':sid'=>$s['sid']])
            ->all();
            
            if(!empty($servhist)) {
                if(count($servhist) > 1) {
                    foreach($servhist as $sh) {
                        if($s['date'] < date('Y-m-d', strtotime($sh['date']))) {
                            $cost = $sh['value'];
                        } 
                    }
                } else {
                    if($s['date'] < date('Y-m-d', strtotime($servhist[0]['date']))) {
                        $cost = $servhist[0]['value'];
                    }
                }
            }
            
            if($cost == 0) {
                $lesson_cost = (new \yii\db\Query())
                ->select('sn.value as value')
                ->from('calc_service s')
                ->leftJoin('calc_studnorm sn', 'sn.id=s.calc_studnorm')
                ->where('s.id=:sid', [':sid'=>$s['sid']])
                ->one();
                
                $cost = $lesson_cost['value'];
            }
            $data = (new \yii\db\Query())
            ->createCommand()
            ->update('calc_report_margin', ['income' => round($cost * $s['cnt'], 2)], ['id'=>$s['id']])
            ->execute();
        }

        
		// задаем коэффициэнт по умолчанию
		$koef = 1;
		// задаем переменную для подсчета суммы начисления
		$accrual = 0;
		// получаем данные по занятиям
		$lessons = (new \yii\db\Query())
		->select('rm.id as id, rm.creation_date as date, rm.student_count as cnt, en.value as tax, jg.calc_edutime as edutime, rm.calc_teacher as teacher, tn.value as time')
		->from('calc_report_margin rm')
        ->leftJoin('calc_journalgroup jg', 'jg.id=rm.calc_journalgroup')
		->leftJoin('calc_accrualteacher at', 'at.id=jg.calc_accrual')
		->leftJoin('calc_edunormteacher ent','ent.id=at.calc_edunormteacher')
		->leftJoin('calc_edunorm en','en.id=ent.calc_edunorm')
        ->leftJoin('calc_service s', 'rm.calc_service=s.id')
        ->leftJoin('calc_timenorm tn', 's.calc_timenorm=tn.id')
		->where('MONTH(rm.creation_date)=10')
		->all();
    
	    $admins = [7, 30];
		foreach($lessons as $lesson){
		    //выбираем коэффициент в зависимости от количества учеников
		    switch($lesson['cnt']){
		    case 1: $koef = 1; break;
		    case 2: $koef = 1; break;
		    case 3: $koef = 1; break;
		    case 4: $koef = 1.1; break;
		    case 5: $koef = 1.2; break;
		    case 6: $koef = 1.3; break;
		    case 7: $koef = 1.4; break;
		    case 8: $koef = 1.5; break;
		    case 9: $koef = 1.6; break;
		    case 10: $koef = 1.7; break;
		    }
			// задаем полную ставку (ставка + надбавка)
			//$fullnorm = $norm;
			// если надбавка больше 0
			//if($lesson['corp'] > 0) {
				// суммируем ее со ставкой
				//$fullnorm = $norm + $edunorm['corp'];
			//} 
		    // считаем сумму начисления
		    switch($lesson['edutime']){
		        // дневное время у всех из ставки вычитается 50 рублей
				case 1:
                if($lesson['date'] < '2016-09-01' && in_array($lesson['teacher'], $admins)) {
                    // для руководителей ставка 0. для остальных -50 р. (с сентября 2016 не используется)
                    $accrual = ($lesson['tax'] - $lesson['tax']) * $lesson['time'] * $koef;
                    // для остальных -50р 
                    //$accrual += ($fullnorm - 50) * $lesson['time'] * $koef;
                }
				else {
					$accrual = ($lesson['tax'] - 50) * $lesson['time'] * $koef;
				}
				break;
				// вечернее время используем полную ставку
				case 2: $accrual = $lesson['tax'] * $lesson['time'] * $koef; break;
				// полурабочее время (с сентября 2016 не используется)
				case 3: $accrual = $lesson['tax'] * $lesson['time'] * (2/3) * $koef; break;
		    }
            //echo '<li>' . $lesson['tax'] . ' ' . $lesson['time'] . ' ' . $koef . ' = ' . $accrual . '</li>';
            
            $data = (new \yii\db\Query())
            ->createCommand()
            ->update('calc_report_margin', ['expense' => round($accrual, 2)], ['id'=>$lesson['id']])
            ->execute();
            
		}
		return 'success!';
	}
*/

    protected static function getTeacherID()
    {
        if(Yii::$app->request->get('TID') && Yii::$app->request->get('TID') != 'all'){
            $tid = Yii::$app->request->get('TID');
        } else {
            $tid = NULL;
        }

        return $tid;
	}
 }
