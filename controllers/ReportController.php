<?php

namespace app\controllers;

use Yii;
use app\models\AccrualTeacher;
use app\models\Moneystud;
use app\models\Office;
use app\models\Report;
use app\models\Sale;
use app\models\Schedule;
use app\models\Tool;
use app\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\Pagination;

class ReportController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['accrual','common','debt','index','margin','payments','plan','sale','salaries'],
                'rules' => [
                    [
                        'actions' => ['accrual','common','debt','index','margin','plan','payments','sale','salaries'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['accrual','common','debt','index','margin','plan','payments','sale','salaries'],
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
    
    public function actionPayments ($start = null, $end = null)
    {
        /* всех кроме руководителей, менеджеров и бухгалтеров редиректим обратно */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4 && (int)Yii::$app->session->get('user.ustatus') !== 8) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        if (Yii::$app->request->isPost) {
            $office = null;
            if ((int)Yii::$app->session->get('user.ustatus') === 4) {
              $office = (int)Yii::$app->session->get('user.uoffice_id');
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                "status" => true,
                "menuData" => Report::getReportTypes(),
                "paymentsData" => [
                    "columns" => Report::getPaymentsReportColumns(),
                    "rows" => Report::getPaymentsReportRows($start, $end, $office)
                ]
            ];
        } else {
            return $this->render('payments');
        }
    }

    public function actionPlan()
	{
        /* всех кроме менеджеров и руководителей редиректим обратно */
        if(Yii::$app->session->get('user.ustatus')!=3 && Yii::$app->session->get('user.ustatus')!=8) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        /* всех кроме менеджеров и руководителей редиректим обратно */
		
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
                $first_day = date('Y-m-d', ($week - 1) * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400 + 86400);
                $last_day = date('Y-m-d', $week * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400);
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
        $oid = NULL;
        $pages = NULL;
        $sign = 1;
        $sign_id = '<';
        $state = 1;
        $state_id = 1;
        $tss = NULL;
        $val = -10;
        
        if (Yii::$app->request->get('TSS')) {
            $tss = Yii::$app->request->get('TSS');
        }
        
        if (Yii::$app->request->get('OID') && Yii::$app->request->get('OID') != 'all') {
            $oid = (int)Yii::$app->request->get('OID');
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
            $oid = (int)Yii::$app->session->get('user.uoffice_id');
        }

        // задаем фильтр по балансу (по умолчанию отрицательный баланс)
        if(Yii::$app->request->get('SIGN')) {
            $sign = Yii::$app->request->get('SIGN');
            if ((int)Yii::$app->request->get('SIGN') === 1) {
                $sign_id = '<';
            } else if ((int)Yii::$app->request->get('SIGN') === 2) {
                $sign_id = '>';
            } else {
                $val = NULL;
            }
        }

        // задаем фильтр по состоянию клиента (по умолчанию активные)
        if (Yii::$app->request->get('STATE')) {
            $state = Yii::$app->request->get('STATE');
            if ((int)Yii::$app->request->get('STATE') === 1) {
                $state_id = 1;
            } else if ((int)Yii::$app->request->get('STATE') === 2) {
                $state_id = 0;
            } else {
                $state_id = NULL;
            }
        } else {
            $state = 1;
        }

        // запрашиваем список студентов
        $stds = (new \yii\db\Query())
        ->select(['id' => 'sn.id', 'name' => 'sn.name', 'debt' => 'sn.debt'])
        ->distinct()
        ->from('calc_studname sn')
        ->leftJoin('calc_studgroup sg', 'sn.id=sg.calc_studname')
        ->leftJoin('calc_groupteacher gt', 'gt.id=sg.calc_groupteacher')
        ->where('sn.visible=:vis', [':vis'=>1])
        ->andFilterWhere([$sign_id,'sn.debt', $val])
        ->andFilterWhere(['sn.active' => $state_id])
        ->andFilterWhere(['gt.calc_office' => $oid])
        ->andFilterWhere(['like', 'sn.name', $tss]);
            
        // делаем клон запроса
        $countQuery = clone $stds;
        // получаем данные для паджинации
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        // задаем параметры для паджинации
        $limit = 20;
        $offset = 0;
        if(Yii::$app->request->get('page')){
            if(Yii::$app->request->get('page')>1&&Yii::$app->request->get('page')<=$pages->totalCount){
                $offset = 20 * (Yii::$app->request->get('page') - 1);
        }
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

        if(!empty($stids)) {
            // запрашиваем услуги назначенные студенту
            $students = (new \yii\db\Query())
            ->select('s.id as sid, s.name as sname, is.calc_studname as stid, SUM(is.num) as num')
            ->distinct()
            ->from('calc_service s')
            ->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
            ->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])                
            ->andWhere(['in','is.calc_studname',$stids])
            ->groupby(['is.calc_studname','s.id'])
            ->orderby(['s.id'=>SORT_ASC])
            ->all();
            // запрашиваем услуги назначенные студенту
            
            // проверяем что у студента есть назначенные услуги
            if(!empty($students)){
                $i = 0;
                // распечатываем массив
                foreach($students as $service){
                    $schedule = Schedule::getStudentSchedule($service['stid']);
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
                    $students[$i]['npd'] = Moneystud::getNextPaymentDay($schedule, $service['sid'], $cnt);
                    $i++;
                }
                unset($service);
                unset($lssns);                  
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

    public function actionIndex()
    {
        $this->layout = 'column2';
        /* всех кроме менеджеров и руководителей редиректим обратно */
        if(Yii::$app->session->get('user.ustatus')!=4 && Yii::$app->session->get('user.ustatus')!=3 && Yii::$app->session->get('user.ustatus')!=8) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        /* всех кроме менеджеров и руководителей редиректим обратно */

        $office = NULL;
        // для менеджеров задаем переменную с id офиса
        if(\Yii::$app->session->get('user.ustatus')==4){
            $office = \Yii::$app->session->get('user.uoffice_id');
        }
        if(\Yii::$app->request->get('OID') && \Yii::$app->request->get('OID')!='all') {
            $office = \Yii::$app->request->get('OID');
        }

        switch(\Yii::$app->request->get('type')){
            case 1: ((\Yii::$app->session->get('user.ustatus')==4) ? $type=8 : $type=1); break;
            case 2: ((\Yii::$app->session->get('user.ustatus')==4) ? $type=8 : $type=2); break;
			case 4: $type = 4; break;
            case 5: $type = 5; break;
            case 6: $type = 6; break;
            case 8: $type = 8; break;
            case 9: $type = 9; break;
            case 10: $type = 10; break;
            default: ((\Yii::$app->session->get('user.ustatus')==4) ? $type=8 : $type=1);
        }
        if($type == 1) {
            return $this->redirect(['report/common']);
        }
	
        // проверяем get-запрос на наличие информации о годе и задаем переменную $year
        if(\Yii::$app->request->get('year')){
            $year = \Yii::$app->request->get('year');
        } else {
            $year = date('Y');
        }
        // проверяем get-запрос на наличие информации о годе и задаем переменную $year
		
		// проверяем get-запрос на наличие информации о месяце и задаем переменную $mon
        if(\Yii::$app->request->get('MON')) {
            if(\Yii::$app->request->get('MON')>=1 && \Yii::$app->request->get('MON')<=12) {
                $mon = \Yii::$app->request->get('MON');
            } else {
                $mon = NULL;
            }
        } else {
            $mon = NULL;
        }
        // проверяем get-запрос на наличие информации о месяце и задаем переменную $mon
		
		// проверяем get-запрос на наличие информации о неделе и задаем переменные $week, $first_day, $last_day
        if(\Yii::$app->request->get('RWID')){
            if(\Yii::$app->request->get('RWID')!='all') {
                $week = Yii::$app->request->get('RWID') - 1;
                $first_day = date('Y-m-d', $week * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400 + 86400);
                $last_day = date('Y-m-d', ($week + 1) * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400);
            } else {
                $first_day = NULL;
                $last_day = NULL;
            }
        } else {
            $week = date('W') - 1;          
            $first_day = date('Y-m-d', $week * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400 + 86400);
            $last_day = date('Y-m-d', ($week + 1) * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400);

        }
        // проверяем get-запрос на наличие информации о неделе и задаем переменные $week, $first_day, $last_day
		
		// проверяем get-запрос на наличие информации о преподавателе и задаем переменную $tid
        if(\Yii::$app->request->get('RTID') && \Yii::$app->request->get('RTID')!='all'){
            $tid = \Yii::$app->request->get('RTID');
        } else {
            $tid = NULL;
        }
        // проверяем get-запрос на наличие информации о преподавателе и задаем переменную $tid
		
        // проверяем get-запрос на наличие термина для поиска и задаем переменную $tss
        if(\Yii::$app->request->get('TSS')){
            $tss = \Yii::$app->request->get('TSS');
        } else {
            $tss = NULL;
        }
        // проверяем get-запрос на наличие термина для поиска и задаем переменную $tss
		
        // выбираем список месяцев
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
        // выбираем список месяцев
		
        // задаем пустые массивы, которые потом будут использоваться в разных отчетах
        $payments = [];
        $invoices = [];
        $students = [];
        $stds = [];
        $debt = [];
        $offices = [];
        $lessons = [];
        $teachers = [];
        $tchrs = [];
        $groups = [];
        $dates = [];
        $common_report = [];
        $pages = [];
        $lcount = [];
        // задаем пустые массивы, которые потом будут использоваться в разных отчетах
		
        // формируем данные для общего отчета
        if($type == 1) {
			// определяем условия выборки
            if($mon && $year) {
                $fd = NULL;
                $ld = NULL;
                $m = $mon;
                $y = $year;
            } elseif(!$first_day && !$last_day && !$mon && $year) {
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
			// определяем условия выборки
			
            // получаем список офисов
            $offices = (new \yii\db\Query())
            ->select('id as oid, name as oname')
            ->from('calc_office')
            ->where('visible=1')
            ->andWhere(['not in','id',['20','17','15','14','13']])
            ->all();
            // получаем список офисов
			
            // получаем оплаты
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
            // получаем оплаты
			
            // получаем счета
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
            // получаем счета
			
            // получаем начисления
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
            // получаем начисления
			
            // получаем часы
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

            // получаем колич студентов
            $subQuery = (new \yii\db\Query())
            ->select('count(DISTINCT sjg.calc_studname) as students')
            //->distinct()
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

            // получаем долги
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
            // задаем начальные переменные для формирования многомерного массива с данными для таблицы
            $i = 0;
            // оплаты
            $pmnts = 0;
            // счета
            $nvcs = 0;
            // скидки
            $dscnt = 0;
            // начисления
            $ccrls = 0;
            // часы
            $hrs = 0;
            // долги
            $dbts = 0;
            // долги
            $sts = 0;
            
            // распечатываем массив офисов
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
                        // суммируем оплаты для последущего получения итогового значения
                        $pmnts = $pmnts + $pay['money'];
                    }
                }
                // рампечатываем массив со счетами
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
            // ставим число побольше чтобы точно не совпадало с id офисов
            $i = 999;
            // добавлем последний вложенный массив в котором будут итоговые суммарные значения по столбцам
            // задаем id офиса - в данном случае совпадает с номером массива
            $common_report[$i]['oid'] = $i;
            // задаем имя массива
            $common_report[$i]['name'] = 'Итого:';
            // задаем итоговую сумму по оплатам
            $common_report[999]['payments'] = $pmnts;
            // задаем итоговвую сумму по счетам
            $common_report[999]['invoices'] = $nvcs;
            // задаем итоговую сумму по скидкам
            $common_report[999]['discounts'] = $dscnt;
            // задаем итоговую сумму по начислениям
            $common_report[999]['accruals'] = $ccrls;
            // задаем итоговую сумму по часам
            $common_report[999]['hours'] = $hrs;
            // задаем итоговую сумму по студентам
            $common_report[999]['students'] = $sts;
            // задаем итоговую сумму по долгам
            $common_report[999]['debts'] = $dbts;

            // уничтожаем временные переменные
            unset($i);
            unset($common_payments);
            unset($common_invoices);
            unset($common_accruals);
            unset($common_hours);
            unset($common_students);
            unset($common_debts);
            unset($pmnts);
            unset($nvcs);
            unset($dscnt);
            unset($ccrls);
            unset($hrs);
            unset($sts);
            unset($dbts);
            unset($o);
            unset($m);
            unset($y);
            unset($fd);
            unset($ld);
            unset($subQuery);
            $offices = [];
        }

		// если указан тип отчета - Маржа 1
		if($type == 2) {
			// определяем условия выборки
            if($mon && $year) {
                $m = $mon;
                $y = $year;
            } else {
                $m = date('m');
                $y = date('Y');
            }
			// определяем условия выборки
			
			$teachers = (new \yii\db\Query())
			->select('t.id as tid, t.name as tname, count(rm.id) as cnt, sum(rm.income) as income, sum(rm.expense) as expense')
			->from('calc_report_margin rm')
			->leftJoin('calc_teacher t', 't.id=rm.calc_teacher')
			->where('rm.deleted!=:vis', [':vis'=>1])
            //->andFilterWhere(['>=', 'rm.creation_date', $fd])
            //->andFilterWhere(['<=', 'rm.creation_date', $ld])
            ->andFilterWhere(['MONTH(rm.creation_date)'=>$m])
            ->andFilterWhere(['YEAR(rm.creation_date)'=>$y])
			->groupBy(['tid', 'tname'])
            ->orderBy(['tname'=>SORT_ASC])
			->all();
		}
		// если указан тип отчета - Маржа 1
		
        // если указан тип отчета - Оплаты
        if($type == 4){
			// определяем условия выборки
            if($mon && $year) {
                $fd = NULL;
                $ld = NULL;
                $m = $mon;
                $y = $year;
            } else {
                $fd = $first_day;
                $ld = $last_day;
                $m = NULL;
                $y = NULL;
            }
			// определяем условия выборки
			
            // получаем список офисов
            $offices = (new \yii\db\Query())
            ->select('id as oid, name as oname')
            ->from('calc_office')
            ->where('visible=1')
            //->andWhere(['not in','id',['6', '20','17','15','14','13']])
            ->andFilterWhere(['id'=>$office])
            ->all();
            // получаем список офисов
			
            // получаем даннные по оплатам
            $payments = (new \yii\db\Query())
            ->select('ms.id as mid, sn.id as sid, sn.name as sname, ms.value as money, ms.value_card as card, ms.value_cash as cash, ms.value_bank as bank, ms.data as date, ms.receipt as receipt, u.name as uname, ms.visible as visible, ms.remain as remain, ms.calc_office as oid')
            ->from('calc_moneystud ms')
            ->leftjoin('calc_studname sn', 'sn.id=ms.calc_studname')
            ->leftJoin('user u', 'u.id=ms.user')
            //->where('ms.visible=:vis', [':vis'=>1])
            ->andFilterWhere(['ms.calc_office'=>$office])
            ->andFilterWhere(['>=', 'ms.data', $fd])
            ->andFilterWhere(['<=', 'ms.data', $ld])
            ->andFilterWhere(['MONTH(ms.data)'=>$m])
            ->andFilterWhere(['YEAR(ms.data)'=>$y])
            ->orderby(['ms.data'=>SORT_DESC, 'ms.id'=>SORT_DESC])
            ->all();
            // получаем даннные по оплатам
			
            $i = 0;
            // формируем массив дат, для последующей группировки оплат
            foreach($payments as $p){
                $dates[$i] = $p['date'];
                $i++;
            }
            unset($p);
            // проверяем что массив не пустой (если оплат не было)
            if(!empty($dates)){
                // избавляемся от дублей
                $dates = array_unique($dates);
            }
        }
        // если указан тип отчета - Оплаты
		
        // если указан тип отчета - Счета
        if($type == 5){
        	// определяем условия выборки
            if($mon && $year) {
                $fd = NULL;
                $ld = NULL;
                $m = $mon;
                $y = $year;
            } else {
                $fd = $first_day;
                $ld = $last_day;
                $m = NULL;
                $y = NULL;
            }
			// определяем условия выборки

            // получаем данные по счетам
            $invoices = (new \yii\db\Query())
            ->select('is.id as iid, sn.id as sid, sn.name as sname, u.name as uname, is.value as money, is.visible as visible, is.done as done, is.num as num, is.calc_service as id, is.data as date, is.remain as remain')
            ->from('calc_invoicestud is')
            ->leftJoin('user u', 'u.id=is.user')
            ->leftJoin('calc_studname as sn', 'sn.id=is.calc_studname')
            ->andFilterWhere(['is.calc_office'=>$office])
            ->andFilterWhere(['>=', 'is.data', $fd])
            ->andFilterWhere(['<=', 'is.data', $ld])
            ->andFilterWhere(['MONTH(is.data)'=>$m])
            ->andFilterWhere(['YEAR(is.data)'=>$y])
            ->orderby(['is.data'=>SORT_DESC, 'is.id'=>SORT_DESC])
            ->all();
            // получаем данные по счетам
			
            $i = 0;
            // формируем массив дат, для последующей группировки счетов
            foreach($invoices as $inv){
                $dates[$i] = $inv['date'];
                $i++;
            }
            unset($inv);
            // проверяем что массив не пустой (если счетов не было)
            if(!empty($dates)){
                // избавляемся от дублей
                $dates = array_unique($dates);
            }
        } 

        // если указан тип отчета - Долги
        if($type == 6){
            // задаем фильтр по балансу (по умолчанию отрицательный баланс)
            if(Yii::$app->request->get('SIGN')) {
                if(Yii::$app->request->get('SIGN')==1) {
                    $sign = '<';
                    $val = 0;
                } elseif(Yii::$app->request->get('SIGN')==2) {
                    $sign = '>';
                    $val = 0;
                } else {
                    $sign = '<';
                    $val = NULL;
                }
            } else {
                $sign = '<';
                $val = 0;
            }
            // задаем фильтр по состоянию клиента (по умолчанию активные)
            if(Yii::$app->request->get('state')) {
                if(Yii::$app->request->get('STATE')==1) {
                    $state = 1;
                } elseif(Yii::$app->request->get('STATE')==2) {
                    $state = 0;
                } else {
                    $state = NULL;
                }
            } else {
                $state = 1;
            }
            // получаем список офисов
            $offices = (new \yii\db\Query())
            ->select('id as oid, name as oname')
            ->from('calc_office')
            ->where('visible=1')
            //->andWhere(['not in','id',['20','17','15','14','13']])            
            ->all();
            // запрашиваем список студентов
            $stds = (new \yii\db\Query())
            ->select(['id' => 'sn.id', 'name' => 'sn.name', 'debt' => 'sn.debt'])
            ->distinct()
            ->from('calc_studname sn')
            ->leftJoin('calc_studgroup sg', 'sn.id=sg.calc_studname')
            ->leftJoin('calc_groupteacher gt', 'gt.id=sg.calc_groupteacher')
            ->where('sn.visible=:vis', [':vis'=>1])
            ->andFilterWhere([$sign,'sn.debt2', $val])
            ->andFilterWhere(['sn.active'=>$state])
            ->andFilterWhere(['gt.calc_office'=>$office])
            ->andFilterWhere(['like', 'sn.name', $tss]);
            
            // делаем клон запроса
            $countQuery = clone $stds;
            // получаем данные для паджинации
            $pages = new Pagination(['totalCount' => $countQuery->count()]);

            // задаем параметры для паджинации
            $limit = 20;
            $offset = 0;
            if(Yii::$app->request->get('page')){
                if(Yii::$app->request->get('page')>1&&Yii::$app->request->get('page')<=$pages->totalCount){
                    $offset = 20 * (Yii::$app->request->get('page') - 1);
            }
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
            unset($s);
            //unset($stds);
            if(!empty($stids)) {
                // задаем пустой массив для общего списка студентов
                //$stdnts = [];
                // оставляем в массиве со студентами только уникальные значения
                //$tmp_stdnts = array_unique($tmp_stdnts);
                //$i = 0;
                //foreach($tmp_stdnts as $key => $value) {
                    // заполняем массив id-шниками студентов
                //    $stids[$i] = $key;
                    // заполняем массив общим списком студентов
                //  $stdnts[$i]['id'] = $key;
                //  $stdnts[$i]['name'] = $value;
                //  $stdnts[$i]['debt'] = $this->studentDebt($key);
                //    $i++;
                //}
                // уничтожаем ненужные переменные
                //unset($key);
                //unset($value);
                
                // запрашиваем услуги назначенные студенту
                $students = (new \yii\db\Query())
                ->select('s.id as sid, s.name as sname, is.calc_studname as stid, SUM(is.num) as num')
                ->distinct()
                ->from('calc_service s')
                ->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
                ->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])                
                ->andWhere(['in','is.calc_studname',$stids])
                ->groupby(['is.calc_studname','s.id'])
                ->orderby(['s.id'=>SORT_ASC])
                ->all();
                // запрашиваем услуги назначенные студенту
                
                // проверяем что у студента есть назначенные услуги
                if(!empty($students)){
                    $i = 0;
                    // распечатываем массив
                    foreach($students as $service){
                        // запрашиваем из базы колич пройденных уроков
                        $lssns = (new \yii\db\Query())
                        ->select('COUNT(sjg.id) AS cnt')
                        ->from('calc_studjournalgroup sjg')
                        ->leftjoin('calc_groupteacher gt', 'sjg.calc_groupteacher=gt.id')
                        ->leftjoin('calc_journalgroup jg', 'sjg.calc_journalgroup=jg.id')
                        ->where('jg.view=:vis and jg.visible=:vis and (sjg.calc_statusjournal=:vis or sjg.calc_statusjournal=:stat) and gt.calc_service=:sid and sjg.calc_studname=:stid', [':vis'=>1, 'stat'=>3, ':sid'=>$service['sid'], ':stid'=>$service['stid']])
                        ->one();
                        // считаем остаток уроков
                        $students[$i]['num'] = $students[$i]['num'] - $lssns['cnt'];                        
                        $i++;
                    }
                    unset($service);
                    unset($lssns);                  
                }
                // проверяем что у студента есть назначенные услуги
            }
        }
        // если указан тип отчета - Журналы   
        if($type == 8) {
            // обращаемся к функции формирующей массивы с данными для отчета
            list($teachers, $lessons, $groups, $tchrs, $pages, $lcount) = $this->reportJournals($tid, $office);
        }

        // если указан тип отчета - начисления   
        if($type == 9) {
            // обращаемся к функции формирующей массивы с данными для отчета
            list($teachers, $lessons, $groups, $pages, $tchrs) = $this->reportAccruals($tid);
        }
        
        // если указан тип отчета - академические долги   
        if($type == 10) {
            // обращаемся к функции формирующей массивы с данными для отчета
            //list($teachers, $lessons, $groups, $pages, $tchrs) = $this->reportAccruals($tid);
            
            //$st_inv = (new \yii\db\Query())
            //->select('COUNT(is.num) as cnt')
            //->from('calc_invoicestud is')
            //->where('is.calc_studname=sn.id AND is.visible=:one', [':one'=>1]);
            
            // запрашиваем список студентов
            $students = (new \yii\db\Query())
            ->select('sn.id as id, sn.name as name')
            //->addSelect(['inv' => $st_inv])
            ->distinct()
            //->from('calc_studname sn')
            //->leftJoin('calc_studgroup sg', 'sn.id=sg.calc_studname')
            //->leftJoin('calc_groupteacher gt', 'gt.id=sg.calc_groupteacher')
            ->from('calc_schedule sch')
            ->leftJoin('calc_studgroup sg', 'sg.calc_groupteacher=sch.calc_groupteacher')
            ->leftJoin('calc_studname sn', 'sg.calc_studname=sn.id')
            ->where('sn.visible=:vis AND sch.visible=:vis AND sch.calc_groupteacher!=:zero AND sg.visible=:vis', [':vis' => 1, ':zero' => 0])
            ->andFilterWhere(['sch.calc_office'=>$office])
            ->limit(10)
            ->orderby(['sn.name'=>SORT_ASC])
            ->all();
            
            $stids = NULL;
            $gids = NULL;
            
            if(!empty($students)) {
                foreach($students as $s) {
                    $stids[] = $s['id'];
                    //$gids[] = $s['gid'];
                }
                unset($s);
                
                $stids = array_unique($stids);
                //$gids = array_unique($gids);
                
                $subQuery = (new \yii\db\Query())
                ->select('COUNT(sjg.id) AS cnt')
                ->from('calc_studjournalgroup sjg')
                ->leftjoin('calc_groupteacher gt', 'sjg.calc_groupteacher=gt.id')
                ->leftjoin('calc_journalgroup jg', 'sjg.calc_journalgroup=jg.id')
                ->where('jg.view=:vis and jg.visible=:vis and (sjg.calc_statusjournal=:vis or sjg.calc_statusjournal=:stat) and gt.calc_service=s.id and sjg.calc_studname=is.calc_studname', [':vis'=>1, 'stat'=>3]);
                
                $invoices = (new \yii\db\Query())
                ->select('s.id as sid, s.name as sname, is.calc_studname as stid, SUM(is.num) as inum')
                ->addSelect(['lnum' => $subQuery])
                ->distinct()
                ->from('calc_service s')
                ->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
                ->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])                
                ->andFilterWhere(['in', 'is.calc_studname', $stids])
                ->groupby(['is.calc_studname','s.id'])
                ->orderby(['s.id' => SORT_ASC])
                ->all();
                
                $lessons = (new \yii\db\Query())
                ->select('gt.calc_service as sid, sch.calc_denned as day')
                ->from('calc_schedule sch')
                ->leftJoin('calc_groupteacher gt', 'gt.id=sch.calc_groupteacher')
                ->leftJoin('calc_studgroup sg', 'sg.calc_groupteacher=gt.id')
                ->where('sch.visible=:one', [':one' => 1])
                ->andWhere(['in', 'sg.calc_studname', $stids])
                ->all();
            }
            
            $stids = NULL;
            $gids = NULL;
        }
        // если указан тип отчета - академические долги

        return $this->render('index',[
		    'months' => $months,
            'common_report' => $common_report,
            'offices' => $offices,
            'payments' => $payments,
            'invoices' => $invoices,
            'dates' => $dates,
            'students' => $students,
            'stds' => $stds,
            'teachers' => $teachers,
            'lessons' => $lessons,
            'groups' => $groups,
            'pages' => $pages,
            'debt' => $debt,
            'pages' => $pages,
            'tchrs' => $tchrs,
            'lcount' => $lcount,
        ]);
    }
    
    /* функция расчета долга студента
    protected function studentDebt($id) {
        
        // задаем переменную в которую будет подсчитан долг по занятиям
        $debt_lessons = 0;
        // задаем переменную в которую будет подсчитан долг по разнице между счетами и оплатами
        $debt_common = 0;
        // полный долг
        $debt = 0;
        
        // получаем информацию по счетам
        $invoices_sum = (new \yii\db\Query())
        ->select('sum(value) as money')
        ->from('calc_invoicestud')
        ->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
        ->one();
        
        // получаем информацию по оплатам
        $payments_sum = (new \yii\db\Query())
        ->select('sum(value) as money')
        ->from('calc_moneystud')
        ->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
        ->one();
        
        // считаем разницу как базовый долг
        $debt_common = $payments_sum['money'] - $invoices_sum['money'];
        
        // запрашиваем услуги назначенные студенту
        $services = (new \yii\db\Query())
        ->select('s.id as sid, s.name as sname, SUM(is.num) as num')
        ->distinct()
        ->from('calc_service s')
        ->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
        ->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])
        ->andWhere(['is.calc_studname'=>$id])
        ->groupby(['is.calc_studname','s.id'])
        ->orderby(['s.id'=>SORT_ASC])
        ->all();
        
        // проверяем что у студента есть назначенные услуги
        if(!empty($services)){
            $i = 0;
            // распечатываем массив
            foreach($services as $service){
                // запрашиваем из базы колич пройденных уроков
                $lessons = (new \yii\db\Query())
                ->select('COUNT(sjg.id) AS cnt')
                ->from('calc_studjournalgroup sjg')
                ->leftjoin('calc_groupteacher gt', 'sjg.calc_groupteacher=gt.id')
                ->leftjoin('calc_journalgroup jg', 'sjg.calc_journalgroup=jg.id')
                ->where('jg.view=:vis and jg.visible=:vis and (sjg.calc_statusjournal=:vis or sjg.calc_statusjournal=:stat) and gt.calc_service=:sid and sjg.calc_studname=:stid', [':vis'=>1, 'stat'=>3, ':sid'=>$service['sid'], ':stid'=>$id])
                ->one();

                // считаем остаток уроков
                $services[$i]['num'] = $services[$i]['num'] - $lessons['cnt'];
                $i++;
            }
            // уничтожаем переменные
            unset($service);
            unset($lessons);
            
            foreach($services as $s) {
                if($s['num'] < 0){
                        $lesson_cost = (new \yii\db\Query())
                        ->select('(value/num) as money')
                        ->from('calc_invoicestud')
                        ->where('visible=:vis and calc_studname=:stid and calc_service=:sid', [':vis'=>1, ':stid'=>$id, ':sid'=>$s['sid']])
                        ->orderby(['id'=>SORT_DESC])
                        ->one();
                        
                        $debt_lessons = $debt_lessons + $s['num'] * $lesson_cost['money'];
                }               
            }
        }
        unset($services);
        $debt = $debt_common + $debt_lessons;
        $debt = number_format($debt, 1, '.', ' ');
        return $debt;
    }
    */
    
    /*
    * метод выборки данных для построения отчета по Журналам 
    */
    
    protected function reportJournals($tid, $office) 
    {
    
        // формируем массив преподавателей для селекта
        $teachers = (new \yii\db\Query())
        ->select('t.id as tid, t.name as tname')
        ->distinct()
        ->from('calc_teachergroup tg')
        ->leftJoin('calc_teacher t', 't.id=tg.calc_teacher')
        ->leftJoin('calc_groupteacher gt', 'gt.id=tg.calc_groupteacher')
        ->where('gt.visible=:vis and tg.visible=:vis', [':vis'=>1])
        ->andFilterWhere(['t.id'=>$tid])
        ->andFilterWhere(['gt.calc_office'=>$office]);
            
        // делаем клон запроса
        $countQuery = clone $teachers;
        // получаем данные для паджинации
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $limit = 10;
        $offset = 0;
        if(Yii::$app->request->get('page')){
            if(Yii::$app->request->get('page')>1&&Yii::$app->request->get('page')<=$pages->totalCount){
                $offset = 10 * (Yii::$app->request->get('page') - 1);
            }
        }
        // доделываем запрос и выполняем
        $teachers = $teachers->orderBy(['t.name'=>SORT_ASC])->limit($limit)->offset($offset)->all();            

        $teachersall = $countQuery->orderBy(['t.name'=>SORT_ASC])->all();
        unset($countQuery);
        
        // зададим пустое значение, оно будет использоваться если фильтр по преподавателю не задан
        $tids = NULL;
        $tchrs = NULL;
        $lcount = [];
        // формируем массив с id преподавателей, для ситуации когда фильтр по преподавателю не задан
        if(!$tid) {
            $i = 0;
            foreach($teachers as $t) {
                // массив id-шников для запроса занятий
                $tids[$i] = $t['tid'];
                // массив преподавателей для вьюза
                $tchrs[$t['tid']] = $t['tname'];
                // массив занятий преподавателя
                $lcount[$t['tid']]['totalCount'] = 0;
                $i++;
            }
            unset($i);
            unset($t);
        } else {
            foreach($teachersall as $t) {
                if($t['tid']==$tid) {
                    // массив преподавателей для вьюза
                    $tchrs[$t['tid']] = $t['tname'];
                    // массив занятий преподавателя
                    $lcount[$t['tid']]['totalCount'] = 0;
                }
            }
            unset($i);
            unset($t);
        }

        if(!empty($tchrs)) {
            // получаем данные по занятиям
            $lessons = (new \yii\db\Query())
            ->select('jg.id as lid, jg.calc_groupteacher as gid, jg.data as date, jg.done as done, jg.calc_teacher as tid, t.name as tname, jg.description as desc, jg.visible as visible')
            ->from('calc_journalgroup jg')
            ->leftJoin('calc_teacher t', 't.id=jg.calc_teacher')
            ->leftJoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
            ->where('jg.view!=:vis and jg.visible=:vis and jg.user!=:null', [':vis'=>1, ':null'=>0])
            ->andFilterWhere(['gt.calc_office'=>$office])
            ->andFilterWhere(['jg.calc_teacher'=>$tid])
            ->andFilterWhere(['in', 'jg.calc_teacher', $tids])
            ->orderby(['t.name'=>SORT_ASC, 'jg.data'=>SORT_DESC])
            ->all();

            // выбираем группы преподавателей
            $groups = (new \yii\db\Query())
            ->select('tg.calc_groupteacher as gid, tg.calc_teacher as tid, s.id as sid, s.name as service, el.name as ename, tn.value as hours')
            ->from('calc_teachergroup tg')
            ->leftJoin('calc_groupteacher gt', 'gt.id=tg.calc_groupteacher')
            ->leftJoin('calc_service s', 's.id=gt.calc_service')
            ->leftJoin('calc_timenorm tn', 'tn.id=s.calc_timenorm')
            ->leftJoin('calc_edulevel el', 'el.id=gt.calc_edulevel')
            ->where('gt.visible=:vis', [':vis'=>1])
            ->andFilterWhere(['gt.calc_office'=>$office])
            ->andFilterWhere(['tg.calc_teacher'=>$tid])
            ->andFilterWhere(['in', 'tg.calc_teacher', $tids])              
            ->orderby(['tg.id'=>SORT_ASC])
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
            unset($t);
            unset($g);
        } else {
            $lessons = [];
            $groups = [];
        }
        $teachers = $teachersall;
        unset($teachersall);
        unset($tids);
        return array ($teachers, $lessons, $groups, $tchrs, $pages, $lcount);
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
