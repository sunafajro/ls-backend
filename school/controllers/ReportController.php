<?php

namespace school\controllers;

use common\components\helpers\DateHelper;
use school\controllers\base\BaseController;
use school\models\Office;
use school\models\reports\CommonReport;
use school\models\reports\DebtsReport;
use school\models\reports\GradesReport;
use school\models\reports\InvoicesReport;
use school\models\reports\JournalReport;
use school\models\reports\LoginsReport;
use school\models\reports\MarginsReport;
use school\models\reports\OfficePlanReport;
use school\models\reports\PaymentsReport;
use school\models\reports\PollsReport;
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
use yii\web\ForbiddenHttpException;

/**
 * Class ReportController
 * @package school\controllers
 */
class ReportController extends BaseController
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
            'commissions',
            'logins',
            'polls',
            'grades',
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
     * Отчет по Начислениям
     * @param string|null $tid
     * @param string|null $month
     * @param string|null $year
     * @param string|null $page
     *
     * @return mixed
     */
    public function actionAccrual(string $tid = null, string $month = null, string $year = null, string $page = null)
    {
        $this->layout = 'main-2-column';
        
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
     * Отчет по Марже
     * @param string|null $start
     * @param string|null $end
     * @return mixed
     */
    public function actionMargin(string $start = null, string $end = null)
    {
        $this->layout = 'main-2-column';

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
     * Отчет по Оплатам
     * @param string|null $start
     * @param string|null $end
     * @param string|null $officeId
     *
     * @return mixed
     */
    public function actionPayments (string $start = null, string $end = null, string $officeId = null)
    {
        $this->layout = 'main-2-column';

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
     */
    public function actionInvoices(string $start = null, string $end = null, string $officeId = null)
    {
        $this->layout = 'main-2-column';

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
     * План по офисам
     * @param string|null $officeId
     * @param string|null $nextMonth
     * @return mixed
     */
    public function actionOfficePlan(string $officeId = null, string $nextMonth = null)
	{
        $this->layout = 'main-2-column';

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
     * @param string|null $end
     * @param string|null $teacherId
     * @param string|null $start
     *
     * @return mixed
     */
    public function actionSalaries (string $start = null, string $end = null, string $teacherId = null)
    {
        $this->layout = 'main-2-column';

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
     */
    public function actionSale(string $page = null)
    {
        $this->layout = 'main-2-column';

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
     */
    public function actionCommon(string $start = null, string $end = null)
    {
        $this->layout = 'main-2-column';

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
     */
    public function actionLessons(string $end = null, string $start = null)
    {
        $this->layout = 'main-2-column';

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
     */
    public function actionCommissions(string $end = null, string $start = null)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;

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
     */
    public function actionJournals(string $corporate = null, string $officeId = null, string $teacherId = null, string $page = null)
    {
        $this->layout = 'main-2-column';

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
     * Отчет по нагрузке преподавателей
     * @param null $start
     * @param null $end
     * @param null $teacherId
     * @param int $limit
     * @param int $offset
     *
     * @return mixed
     */
    public function actionTeacherHours($start = null, $end = null, $teacherId = null, $limit = 10, $offset = 0)
    {
        $this->layout = 'main-2-column';

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

    /**
     * Отчет по посещениям
     * @param string|null $start
     * @param string|null $end
     *
     * @return mixed
     */
    public function actionLogins(string $start = null, string $end = null)
    {
        $this->layout = 'main-2-column';

        $report = new LoginsReport([
            'startDate' => $start,
            'endDate'   => $end,
        ]);

        return $this->render('logins', [
            'dataProvider' => $report->prepareReportData(),
            'end'    => date('d.m.Y', strtotime($report->endDate)),
            'start'  => date('d.m.Y', strtotime($report->startDate)),
        ]);
    }

    /**
     * Отчет по посещениям
     * @param string|null $start
     * @param string|null $end
     *
     * @return mixed
     */
    public function actionPolls(string $start = null, string $end = null, string $pollId = null)
    {
        $this->layout = 'main-2-column';

        $report = new PollsReport([
            'startDate' => $start,
            'endDate' => $end,
            'pollId' => $pollId,
        ]);

        return $this->render('polls', [
            'dataProvider' => $report->prepareReportData(),
            'totals' => $report->prepareTotals(),
            'end' => date('d.m.Y', strtotime($report->endDate)),
            'start' => date('d.m.Y', strtotime($report->startDate)),
            'poll' => $report->poll,
        ]);
    }

    /**
     * Отчет по аттестациям
     * @param string|null $start
     * @param string|null $end
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGrades(string $start = null, string $end = null)
    {
        $this->layout = 'main-2-column';

        $report = new GradesReport([
            'startDate' => $start,
            'endDate' => $end,
        ]);

        [$dataProvider, $searchModel] = $report->prepareReportData(\Yii::$app->request->get());
        return $this->render('grades', [
            'dataProvider' => $dataProvider,
            'end' => date('d.m.Y', strtotime($report->endDate)),
            'start' => date('d.m.Y', strtotime($report->startDate)),
            'searchModel' => $searchModel,
        ]);
    }
}
