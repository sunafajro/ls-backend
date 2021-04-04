<?php

namespace school\controllers;

use common\components\helpers\DateHelper;
use school\models\Office;
use school\models\reports\CommonReport;
use school\models\reports\DebtsReport;
use school\models\reports\InvoicesReport;
use school\models\reports\JournalReport;
use school\models\reports\MarginsReport;
use school\models\reports\OfficePlanReport;
use school\models\reports\PaymentsReport;
use school\models\reports\SalariesReport;
use school\models\reports\SalesReport;
use school\models\reports\TeacherHoursReport;
use school\models\Student;
use school\models\searches\StudentCommissionSearch;
use school\models\AccrualTeacher;
use school\models\Auth;
use school\models\searches\LessonSearch;
use school\models\Report;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

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
        $this->layout = 'main-2-column';
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
            'officeId'      => $report->officeId,
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
            'officeId' => $report->officeId,
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
			'nextMonth'  => $report->nextMonth,
            'officeId'   => $report->officeId,
		]);
	}

    /**
     * Отчет по зарплатам
     * @param null $end
     * @param int $limit
     * @param int $offset
     * @param null $tid
     * @param null $start
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionSalaries (string $start = null, string $end = null, string $teacherId = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 8])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $report = new SalariesReport([
            'startDate' => $start,
            'endDate' => $end,
            'teacherId' => $teacherId,
        ]);

        list($salaries, $teacherId) = $report->prepareReportData();

        return $this->render('salaries', [
            'salaries' => $salaries,
            'end'      => date('d.m.Y', strtotime($report->endDate)),
            'start'    => date('d.m.Y', strtotime($report->startDate)),
            'teacherId' => $teacherId,
        ]);
    }

    /**
     * Отчет по скидкам
     * @param string|null $page
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionSale(string $page = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $report = new SalesReport([
            'page' => $page,
        ]);
        list($sales, $clients, $params) = $report->prepareReportData();

		return $this->render('sale',[
		    'params'  => $params,
            'sales'   => $sales,
            'clients' => $clients,
		]);
    }

    /**
     * Общий отчет по офисам
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
     * Отчет по долгам
     *
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
            'page'        => $page,
            'pages'       => $pages,
            'studentList' => $studentList,
            'students'    => $students,
            'totalDebt'   => Student::getDebtsTotalSum($officeId),
            'name'        => $report->name,
            'state'       => $report->state,
            'type'        => $report->type,
            'officeId'    => $report->officeId,
        ]);
    }

    /**
     * Отчет по занятиям
     *
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
     * Отчет по комиссиям
     *
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
     * Отчет по Журналам
     *
     * @param string|null $corporate
     * @param string|null $officeId
     * @param string|null $teacherId
     * @param string|null $page
     * @return mixed
     * @throws ForbiddenHttpException
     */
    
    public function actionJournals(string $corporate = null, string $officeId = null, string $teacherId = null, string $page = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $report = new JournalReport([
            'corporate' => $corporate,
            'officeId'  => $officeId,
            'teacherId' => $teacherId,
            'page'      => $page,
        ]);
        list($groups, $lessons, $teacherNames, $teacherLessonsCount, $pages) = $report->prepareReportData();

        return $this->render('journals', [
            'groups'              => $groups,
            'lessons'             => $lessons,
            'teacherNames'        => $teacherNames,
            'teacherLessonsCount' => $teacherLessonsCount,
            'corporate'           => $report->corporate,
            'officeId'            => $report->officeId,
            'teacherId'           => $report->teacherId,
            'page'                => $report->page,
            'pages'               => $pages,
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
            'teacherId'    => $report->teacherId,
        ]);
    }
}
