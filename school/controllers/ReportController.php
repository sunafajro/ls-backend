<?php

namespace school\controllers;

use common\components\helpers\DateHelper;
use school\models\Office;
use school\models\reports\CommonReport;
use school\models\reports\DebtsReport;
use school\models\reports\InvoicesReport;
use school\models\reports\MarginsReport;
use school\models\reports\OfficePlanReport;
use school\models\reports\PaymentsReport;
use school\models\reports\TeacherHoursReport;
use school\models\Sale;
use school\models\Student;
use school\models\searches\StudentCommissionSearch;
use school\models\Teacher;
use school\models\AccrualTeacher;
use school\models\Auth;
use school\models\User;
use school\models\searches\LessonSearch;
use school\models\Report;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\data\Pagination;

/**
 * Class ReportController
 * @package school\controllers
 */
class ReportController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors() : array
    {
        $rules = [
            'accrual',
            'common',
            'debt',
            'index',
            'invoices',
            'journals',
            'margin',
            'payments',
            'office-plan',
            'sale',
            'salaries',
            'teacher-hours',
            'commissions'
        ];
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => $rules,
                'rules' => [
                    [
                        'actions' => $rules,
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => $rules,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;

        if (in_array($auth->roleId,  [3, 8])) {
            return $this->redirect(['report/common']);
        } else if($auth->roleId === 4) {
            return $this->redirect(['report/journals']);
        } else if($auth->roleId === 6) {
            return $this->redirect(['report/lessons']);
        } else if ($auth->id === 296) {
            return $this->redirect(['report/teacher-hours']);
        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }
    }

    /**
     * отчет по Начислениям
     * @param string|null $tid
     * @param string|null $month
     * @param string|null $year
     * @param string|null $page
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionAccrual(string $tid = null, string $month = null, string $year = null, string $page = null)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        
        /** @var array */
        $groups   = [];
        /** @var int */
        $limit    = 10;
        /** @var int */
        $offset   = 0;
        /** @var array */
        $list     = [];
        /** @var int|null */
        $month    = $month !== 'all' && !is_null($month) ? (int)$month : null;
        /** @var int|null */
        $year     = $year !== 'all' && !is_null($year) ? (int)$year : null;
        /** @var int|null */
        $tid      = $tid !== 'all' && !is_null($tid) ? (int)$tid : null;

        /** @var array */
        $listTeachers = AccrualTeacher::getTeachersWithViewedLessonsIds();

        /** @var int */
        $pages = count($listTeachers);

        /** @var array */
        $teachersList = AccrualTeacher::getTeachersWithViewedLessonsList();

        /* высчитываем смещение относительно начала массива */
        if ($page && (int)$page <= ceil($pages/$limit)) {
            $offset = 10 * ((int)$page - 1);
        }
        
        /* если преподаватель не задан */
        if (!$tid || $tid == 'all') {
            /* вырезаем из массива 10 преподавателей с соответствующим смещением */
            $list = array_slice($listTeachers, $offset, $limit);
        } else {
            $list[0] = $tid;
        }
        
        /** @var array */
        $teachers = AccrualTeacher::getTeachersWithViewedLessonsInfo($list);
        
        /** @var array */
        $order = ['jg.calc_teacher' => SORT_ASC, 'jg.calc_groupteacher' => SORT_DESC, 'jg.id' => SORT_DESC];
        /** @var array */
        $lessons = AccrualTeacher::getViewedLessonList($list, $order, null, DateHelper::getDateRangeByMonth($month, $year));
        
        // создаем массив с данными по группам и суммарному колич часов
        foreach ($lessons as $i => $lesson) {
            $groups[$lesson['groupId']][$lesson['teacherId']]['teacherId'] = $lesson['teacherId'];
            $groups[$lesson['groupId']][$lesson['teacherId']]['groupId']   = $lesson['groupId'];
            $groups[$lesson['groupId']][$lesson['teacherId']]['company']   = $lesson['company'];
            $groups[$lesson['groupId']][$lesson['teacherId']]['service']   = $lesson['service'];
            $groups[$lesson['groupId']][$lesson['teacherId']]['level']     = $lesson['level'];
            $groups[$lesson['groupId']][$lesson['teacherId']]['serviceId'] = $lesson['serviceId'];
            $groups[$lesson['groupId']][$lesson['teacherId']]['office']    = $lesson['office'];
            if (isset($groups[$lesson['groupId']][$lesson['teacherId']]['time'])) {
                $groups[$lesson['groupId']][$lesson['teacherId']]['time'] += $lesson['time'];
            } else {
                $groups[$lesson['groupId']][$lesson['teacherId']]['time'] = $lesson['time'];
            }
            $lessons[$i]['money'] = round(AccrualTeacher::getLessonFinalCost($teachers, $lesson), 2);
        }
        
        return $this->render('accrual', [
            'accruals'      => AccrualTeacher::getAccrualsByTeacherList($list),
            'actionUrl'     => ['report/accrual'],
			'groups'        => $groups,
            'jobPlaces'     => Yii::$app->params['jobPlaces'],
            'lessons'       => $lessons,
            'pages'         => $pages,
            'params'        => [
                'month' => $month,
                'year'  => $year,
                'tid'   => $tid,
                'page'  => $page,
            ],
            'reportList'    => Report::getReportTypeList(),
            'teachers'      => $teachers,
            'teachersList'  => $teachersList,
        ]);
    }

    /**
     * @param string|null $start
     * @param string|null $end
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionMargin(string $start = null, string $end = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $report = new MarginsReport([
            'startDate' => $start,
            'endDate' => $end,
        ]);
		
        return $this->render('margin',[
            'margins'  => $report->prepareReportData(),
            'end'      => date('d.m.Y', strtotime($report->endDate)),
            'start'    => date('d.m.Y', strtotime($report->startDate)),
        ]);
    }

    /**
     * Отчет по оплатам
     * @param string|null $start
     * @param string|null $end
     * @param string|null $officeId
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionPayments (string $start = null, string $end = null, string $officeId = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 8])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $report = new PaymentsReport([
            'startDate' => $start,
            'endDate' => $end,
            'officeId' => $officeId
        ]);

        return $this->render('payments', [
            'payments'      => $report->prepareReportData(),
            'end'           => date('d.m.Y', strtotime($report->endDate)),
            'start'         => date('d.m.Y', strtotime($report->startDate)),
            'officeId'      => $officeId,
        ]);
    }

    /**
     * Отчет по счетам
     * @param string|null $start
     * @param string|null $end
     * @param string|null $officeId
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionInvoices(string $start = null, string $end = null, string $officeId = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $report = new InvoicesReport([
            'startDate' => $start,
            'endDate' => $end,
            'officeId' => $officeId
        ]);
        list($dates, $invoices) = $report->prepareReportData();

        return $this->render('invoices', [
            'dates'    => $dates,
            'invoices' => $invoices,
            'end'      => date('d.m.Y', strtotime($report->endDate)),
            'start'    => date('d.m.Y', strtotime($report->startDate)),
            'officeId' => $officeId,
        ]);
    }

    /**
     * @param string|null $officeId
     * @param string|null $nextMonth
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionOfficePlan(string $officeId = null, string $nextMonth = null)
	{
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 8])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

		$office = $officeId ? Office::find()->byId($officeId)->active()->one() : null;
		if (empty($office)) {
            $office = Office::find()->active()->orderBy('name')->one();
        }
		$officeId = $office->id;

		$report = new OfficePlanReport([
		    'nextMonth' => $nextMonth,
            'officeId' => $officeId,
        ]);

		list($schedule, $lessonPlan, $moneyPlan, $monthName) = $report->prepareReportData();

		return $this->render('office-plan',[
			'groupList'  => $schedule,
			'lessonPlan' => $lessonPlan,
			'moneyPlan'  => $moneyPlan,
			'office'     => $office,
			'monthName'  => $monthName,
			'nextMonth'  => $nextMonth,
            'officeId'   => $officeId,
		]);
	}

    /**
     * @param null $end
     * @param int $limit
     * @param int $offset
     * @param null $tid
     * @param null $start
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionSalaries ($end = null, $limit = 10, $offset = 0, $tid = null, $start = null)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 8])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
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

    /**
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionSale()
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

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
     * @param string|null $start
     * @param string|null $end
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */ 

    public function actionCommon(string $start = null, string $end = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 8])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $report = new CommonReport([
            'startDate' => $start,
            'endDate' => $end,
        ]);

        return $this->render('common', [
            'offices'  => $report->prepareReportData(),
            'end'      => date('d.m.Y', strtotime($report->endDate)),
            'start'    => date('d.m.Y', strtotime($report->startDate)),
        ]);

    }

    /**
     * @param string|null $name
     * @param string $state
     * @param string $type
     * @param string|null $officeId
     * @param string|null $page
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDebt(string $name = null, string $state = '1', string $type = '0', string $officeId = null, string $page = null)
	{
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $report = new DebtsReport([
            'name' => $name,
            'state' => $state,
            'type' => $type,
            'officeId' => $officeId,
            'page' => $page,
        ]);

        list($studentList, $students, $pages) = $report->prepareReportData();

        return $this->render('debt', [
            'pages'       => $pages,
            'studentList' => $studentList,
            'students'    => $students,
            'totalDebt'   => Student::getDebtsTotalSum($officeId),
            'name'        => $name,
            'state'       => $state,
            'type'        => $type,
            'officeId'    => $officeId,
        ]);
    }

    /**
     * Отчет по занятиям
     * @param string|null $start
     * @param string|null $end
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionLessons(string $end = null, string $start = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 6])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        list($start, $end) = DateHelper::prepareWeeklyIntervalDates($start, $end);
        $searchModel = new LessonSearch();
        $params = Yii::$app->request->queryParams;
        $params['start'] = $start;
        $params['end']   = $end;

        return $this->render('lessons', [
            'actionUrl'    => array_merge(['report/lessons'], $params),
            'dataProvider' => $searchModel->search($params),
            'searchModel'  => $searchModel,
            'end'          => date('d.m.Y', strtotime($end)),
            'start'        => date('d.m.Y', strtotime($start)),
        ]);
    }

    /**
     * @param string $end
     * @param string $start
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCommissions(string $end = null, string $start = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 8])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        list($start, $end) = DateHelper::prepareWeeklyIntervalDates($start, $end);
        $searchModel = new StudentCommissionSearch();
        $params = Yii::$app->request->queryParams;
        $params['start'] = $start;
        $params['end']   = $end;
        if ($auth->roleId === 4) {
            $params['StudentCommissionSearch']['officeId'] = $auth->officeId;
        }

        return $this->render('commissions', [
            'actionUrl'    => array_merge(['report/commissions'], $params),
            'dataProvider' => $searchModel->search($params),
            'searchModel'  => $searchModel,
            'end'          => date('d.m.Y', strtotime($end)),
            'start'        => date('d.m.Y', strtotime($start)),
        ]);
    }

    /**
     * метод выборки данных для построения отчета по Журналам
     * @param int $corp
     * @param null $oid
     * @param null $tid
     * @param null $page
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    
    public function actionJournals($corp = 0, $oid = NULL, $tid = NULL, $page = NULL) 
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        // для менеджеров задаем переменную с id офиса
        if ($auth->roleId === 4) {
            $oid = $oid ?? $auth->officeId;
            if ($corp) {
                $oid = NULL;
            }
        }
        // получим массив преподавателей у которых его активные группы
        $teachers = (new Query())
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
            $lessons = (new Query())
            ->select('jg.id as lid, jg.type as type, jg.calc_groupteacher as gid, jg.data as date, jg.done as done, jg.calc_teacher as tid, t.name as tname, jg.description as desc, jg.visible as visible')
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
            $groups = (new Query())
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

    /**
     * Отчет по почасовке преподавателей
     * @param null $start
     * @param null $end
     * @param null $teacherId
     * @param int $limit
     * @param int $offset
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionTeacherHours($start = null, $end = null, $teacherId = null, $limit = 10, $offset = 0)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!(in_array($auth->roleId, [3, 4, 6]) || in_array($auth->id, [296]))) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $report = new TeacherHoursReport([
            'startDate' => $start,
            'endDate'   => $end,
            'teacherId' => $teacherId,
            'limit'     => $limit,
            'offset'    => $offset,
        ]);

        return $this->render('teacher-hours', [
            'teacherHours' => $report->prepareReportData(),
            'end'          => date('d.m.Y', strtotime($report->endDate)),
            'start'        => date('d.m.Y', strtotime($report->startDate)),
            'teacherId'    => $teacherId,
        ]);
    }
}
