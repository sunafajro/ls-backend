<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use school\models\Auth;
use school\models\SpendSuccesses;
use Yii;
use school\models\Contract;
use school\models\Groupteacher;
use school\models\Invoicestud;
use school\models\Journalgroup;
use school\models\Moneystud;
use school\models\Office;
use school\models\Salestud;
use school\models\Schedule;
use school\models\searches\LessonSearch;
use school\models\Service;
use school\models\Student;
use school\models\StudentCommission;
use school\models\Studjournalgroup;
use school\models\forms\StudentMergeForm;
use school\models\User;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

/**
 * Class StudnameController
 * @package school\controllers
 */
class StudnameController extends BaseController
{
    public function behaviors(): array
    {
        $rules = [
            'index', 'view', 'create', 'update', 'delete',
            'detail', 'active', 'inactive', 'merge',
            'change-office', 'update-debt', 'offices',
            'update-settings', 'settings', 'successes',
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'change-office'   => ['post'],
                    'update-debt'     => ['post'],
                    'update-setting'  => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'main-2-column';
        $request = Yii::$app->request;
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;

        // по умолчанию поиск по имени
        $tss = $request->get('TSS') ? $request->get('TSS') : NULL;
        $tss_condition = ['like', 's.name', $tss];
        // если в строке целое число, то поиск по идентификатору
        if ((int)$tss > 0) {
            $tss_condition = ['like', 's.phone', $tss];
        } else if (strpos($tss, '#') !== false) {
            $tss_condition = ['s.id' => substr($tss, 1)];
        }

        $oid = NULL;
        // проверяем GET запрос на наличие переменной OID (фильтр по имени)
        if ($request->get('OID')) {
            if($request->get('OID')=='all'){
                $oid = NULL;
            } else {
                $oid = $request->get('OID');
            }
        } else {
            if ($roleId === 4) {
                $oid = (int)Yii::$app->session->get('user.uoffice_id');
            }
        }

        $state = 1;
        $state_id = 1;
        // проверяем GET запрос на наличие переменной STATE (фильтр по состоянию клиента)
        if ($request->get('STATE')) {
            if ($request->get('STATE')!='all') {
                switch($request->get('STATE')){
                    // студент со статусом "С НАМИ"
                    case 1: $state_id = 1; break;
                    // студент со статусом "НЕ С НАМИ"
                    case 2: $state_id = 0; break;
                }
                $state = (int)$request->get('STATE');
            } else {
                $state = 'all';
                $state_id = NULL;
            }
        }

        $columns = [
            'stid'           => 's.id',
            'stname'         => 's.name',
            'visible'        => 's.visible',
            'birthdate'      => 's.birthdate',
            'phone'          => 's.phone',
            'description'    => 's.description',
            'stinvoice'      => 's.invoice',
            'stmoney'        => 's.money',
            'debt'           => 's.debt',
            'stsex'          => 's.calc_sex',
            'active'         => 's.active',
            'hiddenServices' => "(s.settings->'$.hiddenServices')",
        ];
        // для руководителя и менеджера выводим полный список студентов
        if (in_array($roleId, [3, 4, 6]) || (int)Yii::$app->session->get('user.uid') === 296) {
            // формируем запрос
            $students = (new \yii\db\Query())
            ->select($columns)
            ->from(['s' => Student::tableName()]);
            if ($oid) {
                $students = $students->innerJoin('student_office so', 'so.student_id=s.id');
            }
            $students = $students->where(['s.visible' => 1])
            ->andFilterWhere(['s.active' => $state_id])
            ->andFilterWhere($tss_condition);
            if ($oid) {
                $students = $students->andFilterWhere(['so.office_id' => $oid]);
            }
        } else {
            // формируем запрос
            $students = (new \yii\db\Query())
            ->select($columns)
            ->distinct()
            ->from(['s' => Student::tableName()])
            ->leftjoin('calc_studgroup sg', 'sg.calc_studname = s.id')
            ->leftjoin('calc_teachergroup tg', 'tg.calc_groupteacher = sg.calc_groupteacher')
            ->where(['s.visible' => 1, 'tg.calc_teacher' => $request->get('user.uteacher')])
            ->andFilterWhere(['s.active' => $state_id])
            ->andFilterWhere($tss_condition);
        }
        $countQuery = clone $students;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $limit = 20;
        $offset = 0;
        if ($request->get('page')){
            if($request->get('page') > 1 && $request->get('page') <= $pages->totalCount){
                $offset = 20 * ($request->get('page') - 1);
            }
        }
        $students = $students->orderBy(['s.active' => SORT_DESC, 's.name' => SORT_ASC])->limit($limit)->offset($offset)->all();

		$studentIds = ArrayHelper::getColumn($students, 'stid');
		$services = [];
		if (!empty($studentIds)) {
            $services = Service::getStudentServicesByInvoices($studentIds, []);
            if (!empty($services)) {
                foreach ($services as $i => $service) {
                    $lessons = (new \yii\db\Query())
                    ->select('COUNT(sjg.id) AS cnt')
                    ->from(['sjg' => Studjournalgroup::tableName()])
                    ->leftjoin(['gt' => Groupteacher::tableName()], 'sjg.calc_groupteacher = gt.id')
                    ->leftjoin(['jg' => Journalgroup::tableName()], 'sjg.calc_journalgroup = jg.id')
                    ->where([
                        'jg.view'                => 1,
                        'jg.visible'             => 1,
                        'gt.calc_service'        => $service['id'],
                        'sjg.calc_studname'      => $service['studentId'],
                        'sjg.calc_statusjournal' => [Journalgroup::STUDENT_STATUS_PRESENT, Journalgroup::STUDENT_STATUS_ABSENT_UNWARNED],
                    ])
                    ->one();
                    $services[$i]['num'] = $services[$i]['num'] - $lessons['cnt'];
                }
            }
            // готовим информацию по договорам и добавляем ее в массив клиентов
            $contracts = Contract::getClientContracts($studentIds);
            if (!empty($contracts)) {
                foreach ($students as $key => $val) {
                    foreach ($contracts as $c) {
                        if ((int)$val['stid'] === (int)$c['student']) {
                            if (!isset($students[$key]['contracts'])) {
                                $students[$key]['contracts'] = [];
                            }
                            $students[$key]['contracts'][] = $c;
                        }
                    }
                }
            }
        }

        return $this->render('index', [
            'students'      => array_map(function ($student) {
                $student['hiddenServices'] = Json::decode($student['hiddenServices']) ?? [];
                return $student;
            }, $students),
            'services'      => $services,
            'pages'         => $pages,
            'oid'           => $oid,
            'tss'           => $tss,
            'state'         => $state,
            'offices'       => (new Office())->getOfficesListSimple(),
        ]);
    }

    /**
     * @param int $id
     * 
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        /** @var Student $student */
        $student = $this->findModel($id);
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // проверяем какие данные выводить в карочку преподавателя: 1 - активные группы, 2 - завершенные группы, 3 - счета; 4 - оплаты
        if (Yii::$app->request->get('tab')) {
            switch(Yii::$app->request->get('tab')){
                case 1: $tab = 1; $vis = 1; break;
                case 2: $tab = 2; $vis = 0; break;
                case 3: $tab = 3; break;
                case 4: $tab = 4; break;
                case 5: $tab = 5; break;
                case 6: $tab = 6; break;
                default: {
                    // для менеджеров и руководителей по умолчанию раздел счетов
                    if (in_array($roleId, [3, 4])) {
                        $tab = 3;
                    } else {
                        // всем остальным раздел активных групп
                        $tab = 1;
                    }
                    $vis = 1;
                }
            }
        } else {
            // для менеджеров и руководителей по умолчанию раздел счетов
            if (in_array($roleId, [3, 4])) {
                $tab = 3;
            } else {
                // всем остальным раздел активных групп
                $tab = 1;
            }
            $vis = 1;
        }
        
        /* данные по назначенным скидкам и по колич оплаченных занятий видны только менеджерам и руководителям */
        if (in_array($roleId, [3, 4])) {
            // список скидок клиента
            $studsales = Salestud::getAllClientSales($id);
				
			// постоянная скидка студента
			$permsale = Salestud::getClientPermamentSale($id);

            // расписание студента
            $schedule = new Schedule();
            $studentSchedule = $schedule->getStudentSchedule($id);
            $hiddenServices  = $student->settings['hiddenServices'] ?? [];
            $studentServices = ArrayHelper::getColumn(
                Service::getStudentServicesByInvoices([$student->id], []),
                'id'
            );
            $visibleServices = array_diff($studentServices, $hiddenServices);
            $services = !empty($hiddenServices) && empty($visibleServices)
                ? null :
                $student->getServicesBalance(
                    $visibleServices,
                    $studentSchedule
                );
        } else {
            $studsales = [];
            $permsale = [];
            $services = [];
            $studentSchedule = [];
        }
        
        #region вкладка Счета
        $invoices = [];
        $invcount = [];
        if ($tab == 3) {
            $invoices = Invoicestud::getStudentInvoiceById($id);
            
            $invcount = [1 => 0, 2 => 0, 3 => 0];
            foreach ($invoices as $in) {
                if ($in['idone'] == 0 && $in['ivisible'] == 1) {
                    $invcount[1] = $invcount[1] + 1;
                }
                if ($in['idone'] == 1 && $in['ivisible'] == 1) {
                    $invcount[2] = $invcount[2] + 1;
                }
                if ($in['ivisible'] == 0) {
                    $invcount[3] = $invcount[3] + 1;
                }
            }
        }
        #endregion

        #region вкладка Оплаты
        $payments = [];
        $years = [];
        if ($tab == 4) {
            $payments = Moneystud::getStudentPaymentById($id);
            foreach ($payments as $pay) {
                $years[] = substr($pay['pdate'], 0, 7);
            }
            $years = array_unique($years);
        }
        #endregion

        #region вкладка Комиссии
        $commissions = [];
        if ($tab == 5) {
            // получаем оплаты пользователя
            $commissions = StudentCommission::getStudentCommissionById($id);
            foreach ($commissions as $commission) {
                $years[] = substr($commission['date'], 0, 7);
            }
            $years = array_unique($years);
        }
        #endregion

        #region вкладка Группы
        if ($tab ==1 || $tab == 2) {
            // выбираем данные по группам 
            $groups = (new \yii\db\Query())
            ->select('cgt.id as gid, cs.id as sid, cs.name as sname, cgt.data as gdate, cel.name as elname, ct.id as tid, ct.name as tname, co.name as oname, ctn.value as tnvalue, csg.data as sgdate, cgt.data_visible as gvdate, cgt.visible as gvisible, csg.visible as sgvisible, csg.data_visible as sgvdate')
            ->from('calc_groupteacher cgt')
            ->leftJoin('calc_studgroup csg', 'cgt.id=csg.calc_groupteacher')
            ->leftJoin('calc_service cs', 'cs.id=cgt.calc_service')
            ->leftJoin('calc_timenorm ctn', 'cs.calc_timenorm=ctn.id')
            ->leftJoin('calc_edulevel cel', 'cel.id=cgt.calc_edulevel')
            ->leftJoin('calc_teacher ct', 'ct.id=cgt.calc_teacher')
            ->leftJoin('calc_office co', 'co.id=cgt.calc_office')
            ->where('csg.calc_studname=:id', [':id'=>$id])
            ->andFilterWhere(['cgt.visible'=>$vis])
            ->orderby(['cgt.id'=>SORT_DESC])
            ->all();
            
            if(!empty($groups)){
                $i = 0;
                foreach($groups as $g){
                    $gids[$i] = $g['gid'];
                    $i++;
                }
                unset($g);
                $gids = array_unique($gids);
    
                $lessons = (new \yii\db\Query())
                ->select('cjg.visible as jgvisible, cjg.data as jgdate, csj.name as sjname, csj.id as sjid, csjg.calc_groupteacher as gid')
                ->from('calc_studjournalgroup csjg')
                ->leftJoin('calc_journalgroup cjg', 'csjg.calc_journalgroup=cjg.id')
                ->leftJoin('calc_statusjournal csj', 'csjg.calc_statusjournal=csj.id')
                ->where('cjg.view=:view and csjg.calc_studname=:id', [':view'=>1, ':id'=>$id])
                ->andFilterWhere(['in', 'cjg.calc_groupteacher', $gids])
                ->orderby(['csj.id'=>SORT_ASC, 'cjg.data'=>SORT_DESC, 'csjg.id'=>SORT_DESC])
                ->all();
            } else {
                $lessons = [];
            }
        } else {
            $groups = [];
            $lessons = [];
        }
        #endregion

        #region вкладка Занятия
        if ($tab == 6) {
            $searchOptions = [
                'clientId' => $id,
                'pageSize' => 10
            ];
            if ($roleId === 5) {
                $searchOptions['teacherId'] = Yii::$app->session->get('user.uteacher_id');
            }
            $searchModel  = new LessonSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->get(), $searchOptions);
        }
        #endregion

        return $this->render('view', [
            'commissions'   => $commissions,
            'contracts'     => Contract::getClientContracts($id),
            'dataProvider'  => $dataProvider ?? null,
            'groups'        => $groups,
            'invcount'      => $invcount,
            'invoices'      => $invoices,
            'lessons'       => $lessons,
            'loginStatus'   => $student->getStudentLoginStatus(),
            'model'         => $student,
            'offices'       => [
                'added' => $student->getStudentOffices(),
                'all'   => Office::getOfficesList(),
            ],
            'payments'      => $payments,
            'permsale'      => $permsale,
            'schedule'      => $studentSchedule,
            'searchModel'   => $searchModel ?? null,
            'services'      => $services,
            'studsales'     => $studsales,
            'years'         => $years,
        ]);
    }

    /**
     * Creates a new CalcStudname model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->session->get('user.ustatus')!=3&&Yii::$app->session->get('user.ustatus')!=4) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $userInfoBlock = User::getUserInfoBlock();
        $model = new Student();

        $sexes = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_sex')
        ->all();
        $sex = ArrayHelper::map($sexes ?? [], 'id', 'name');

        $ways = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_way')
        ->all();
        $way = ArrayHelper::map($ways ?? [], 'id', 'name');

        if ($model->load(Yii::$app->request->post())) {
            $model->fname = trim($model->fname);
            $model->lname = trim($model->lname);
            if(isset($model->mname)){
                $model->mname = trim($model->mname);
            }
            $model->name = $model->lname;
            $model->name .= " ".$model->fname;
            if(isset($model->mname)){
                $model->name .= " ".$model->mname;
            }
            $model->history = 0;
            $model->visible = 1;
            $model->active = 1;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'sex' => $sex,
                'way' => $way,
            ]);
        }
    }

    /**
     * Updates an existing CalcStudname model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $userInfoBlock = User::getUserInfoBlock();
        $model = $this->findModel($id);

        $sexes = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_sex')
        ->all();
        $sex = ArrayHelper::map($sexes ?? [], 'id', 'name');

        $ways = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_way')
        ->all();
        $way = ArrayHelper::map($ways ?? [], 'id', 'name');

        if ($model->load(Yii::$app->request->post())) {
            if(isset($model->fname)  && $model->fname!='') {
                $model->fname = trim($model->fname);
            }
            if(isset($model->lname)  && $model->lname!='') {
                $model->lname = trim($model->lname);
            }
            if(isset($model->mname)  && $model->mname!=''){
                $model->mname = trim($model->mname);
            }
            if(isset($model->fname) && isset($model->lname)  && $model->fname!=''  && $model->lname!='') {
                $model->name = $model->lname;
                $model->name .= " ".$model->fname;
            }
            if(isset($model->mname)  && $model->mname!=''){
                $model->name .= " ".$model->mname;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Student data successfully updated!'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to update student data!'));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'sex' => $sex,
                'way' => $way,
            ]);
        }
    }

    /**
     *  метод возвращает профиль клиента в активное состояние
     */
    public function actionActive($id)
    {
        $model = $this->findModel($id);
        if($model->active != 1){
            $model->active = 1;
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Клиент успешно переведен в состояние "С нами"!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось перевести клиента в состояние "С нами"!');
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     *  метод переводит профиль клиента в неактивное состояние
     */
    public function actionInactive($id)
    {
        $model = $this->findModel($id);
        if($model->active != 0){
            $model->active = 0;
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Клиент успешно переведен в состояние "Не с нами"!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось перевести клиента в состояние "Не с нами"!');
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     *  метод помечает профиль клиента как удаленный
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->visible != 0){
            $model->visible = 0;
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Клиент успешно удален!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось удалить клиента!');
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * @param int             $id
     * @param int|null        $type
     * @param string|null     $start
     * @param string|null     $end
     * @param int|string|null $service
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDetail(int $id, int $type = null, string $start = null, string $end = null, $service = null)
    {
        $student = $this->findModel($id);

        $params = [];
        if (!$type) {
            $type = Student::DETAIL_TYPE_INVOICES_PAYMENTS;
        }

        $start = \DateTime::createFromFormat('Y-m-d', $start);
        $end = \DateTime::createFromFormat('Y-m-d', $end);

        if ($start && $end) {
            $params['start'] = $start->format('Y-m-d');
            $params['end'] = $end->format('Y-m-d');
        } else if (!$start && $end) {
            $params['start'] = null;
            $params['end'] = $end->format('Y-m-d');
        } else if ($start && !$end) {
            $params['start'] = $start->format('Y-m-d');
            $params['end'] = null;
        } else {
            $params['start'] = \DateTime::createFromFormat('Y-m-d', date('Y') . '-01-01')->format('Y-m-d');
            $params['end'] = \DateTime::createFromFormat('Y-m-d', date('Y') . '-12-31')->format('Y-m-d');
        }

        $params['service'] = $service;

        $detailData = new ArrayDataProvider([
            'allModels'  => $student->getDetails($type, $params, true),
            'pagination' => false,
            'sort'       => false,
        ]);

        return $this->render('detail', [
            'student'       => $student,
            'detailData'    => $detailData,
            'params'        => $params,
            'type'          => $type,
        ]);
    }

    public function actionMerge($id)
    {
        $student = $this->findModel($id);
        $log = NULL;
        $model = new StudentMergeForm();

        if(Yii::$app->request->post()) {
            if((int)Yii::$app->request->post('StudentMergeForm')['id2'] > 0) {
                $id2 = (int)Yii::$app->request->post('StudentMergeForm')['id2'];
                if($id != $id2) {
                    $transaction = \Yii::$app->db->beginTransaction();
                        try {
                            // пернос данных таблиц от одного пользоватя к другому
                            $log = Student::mergeStudentAccounts($id, $id2);
                            $account2 = $this->findModel($id2);
                            // пересчет баланса
                            $student->invoice = $student->getStudentTotalInvoicesSum();
                            $student->money = $student->getStudentTotalPaymentsSum();
                            $student->debt = $student->money - $student->invoice;
                            // объединение описания
                            $student->description .= $account2->description;
                            if (!$student->save()) {
                                throw new \Exception('Произошла ошибка!');
                            }
                            
                            // очистка дубликата пользователя
                            $account2->invoice = 0;
                            $account2->money = 0;
                            $account2->debt = 0;
                            $account2->visible = 0;
                            $account2->active = 0;
                            $account2->description = 'Данные перенесены в уч. запись №' . $id;
                            if (!$account2->save()) {
                                throw new \Exception('Произошла ошибка!');
                            }
                            
                            $transaction->commit();
                            Yii::$app->session->setFlash('success', 'Процесс переноса данных прошел успешно!');
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Не удалось перенести данные студента!');
                        }
                } else {
                    Yii::$app->session->setFlash('error', 'Идентификаторы студентов не могут быть одинаковыми!');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Ошибочный идентификатор студента!');
            }
        }
        
        return $this->render('merge', [
            'student' => $student,
            'log' => $log,
            'model' => $model,
        ]);
    }

    public function actionChangeOffice($sid, $action)
    {
        $oid = Yii::$app->request->post('office', 0);
        if (Student::findOne($sid) === NULL) {
            throw new NotFoundHttpException(Yii::t('app', 'Client not found!'));
        }
        if (Office::findOne($oid) === NULL) {
            throw new NotFoundHttpException(Yii::t('app', 'Office not found!'));
        }
        if ($action === 'add') {
            $db = (new \yii\db\Query())
            ->createCommand()
            ->insert('student_office',
            [
                'student_id' => $sid,
                'office_id' => $oid,
                'is_main' => false,
            ])
            ->execute();
        } else if ($action === 'delete') {
            $db = (new \yii\db\Query())
            ->createCommand()
            ->delete('student_office',
                'student_id=:sid AND office_id=:oid',
                [':sid' => $sid, ':oid' => $oid]
            )->execute();
        } else if ($action === 'set-main') {
            $db = (new \yii\db\Query())
            ->createCommand()
            ->update('student_office',
            [
                'is_main' => false,
            ],
            'student_id=:sid AND office_id!=:oid AND is_main=TRUE',
            [
                ':sid' => $sid,
                ':oid' => $oid,
            ]
            )
            ->execute();
            $db = (new \yii\db\Query())
            ->createCommand()
            ->update('student_office',
            [
                'is_main' => true,
            ],
            'student_id=:sid AND office_id=:oid',
            [
                ':sid' => $sid,
                ':oid' => $oid,
            ]
            )
            ->execute();
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Action not exists!'));
        }
        $this->redirect(['studname/view', 'id' => $sid]);
    }

    public function actionUpdateDebt($sid)
    {
        $student = Student::findOne($sid);
        if ($student !== NULL) {
            if ($student->updateInvMonDebt()) {
                Yii::$app->session->setFlash('success', 'Баланс студента пересчитан успешно!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось пересчитать баланс клиента!');
            }
            $this->redirect(['studname/view', 'id' => $sid]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Client not found!'));
        }
    }

    public function actionOffices($id)
    {
        $student = Student::findOne($id);
        if ($student === NULL) {
            throw new NotFoundHttpException(Yii::t('app', 'Client not found!'));
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $student->getStudentOffices();
    }

    public function actionUpdateSettings($id)
    {
        /** @var Student $student */
        $student = $this->findModel($id);
        $postData = Yii::$app->request->post();

        switch ($postData['name']) {
            case 'serviceId': {
                if (!isset($postData['value']) && !isset($postData['action'])) {
                    throw new BadRequestHttpException('Отсутствуют необходимые параметры.');
                }
                if ($student->updateServicesList($postData['value'], $postData['action'])) {
                    Yii::$app->session->setFlash('success', 'Список услуг клиента успешно изменен.');
                }
            }
            break;
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionSettings(int $id)
    {
        /** @var Student $student */
        $student = $this->findModel($id);

        $services = $student->getServicesBalance([], []);
        $hiddenServices = $student->settings['hiddenServices'] ?? [];
        foreach ($services as &$service) {
            $service['visible'] = !in_array($service['id'], $hiddenServices);
        }

        $services = new ArrayDataProvider([
            'allModels'  => $services,
            'pagination' => false,
            'sort'       => false,
        ]);

        return $this->render('settings', [
            'model'         => $student,
            'services'      => $services,
        ]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionSuccesses(int $id)
    {
        /** @var Student $student */
        $student = $this->findModel($id);

        $spendSuccessesForm = new SpendSuccesses();
        $spendSuccessesForm->student_id = $id;
        if (Yii::$app->request->isPost) {
            if ($spendSuccessesForm->load(Yii::$app->request->post())) {
                if ($spendSuccessesForm->save()) {
                    return $this->redirect(['studname/successes', 'id' => $student->id]);
                }
            }
        }

        $spendedSuccesses = new ArrayDataProvider([
            'allModels'  => $student->getSpendSuccessesHistory(),
            'pagination' => false,
            'sort'       => false,
        ]);

        return $this->render('successes', [
            'model'              => $student,
            'spendSuccessesForm' => $spendSuccessesForm,
            'spendedSuccesses'   => $spendedSuccesses,
        ]);
    }
  
    /**
     * Finds the Student model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Student the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	/*
	* Метод высчитывает долг клиента и возвращает значение
 	*/
	
//	protected function studentDebt($id) {
//
//		// задаем переменную в которую будет подсчитан долг по занятиям
//		$debt_lessons = 0;
//		// задаем переменную в которую будет подсчитан долг по разнице между счетами и оплатами
//		$debt_common = 0;
//		// полный долг
//		$debt = 0;
//
//		// получаем информацию по счетам
//		$invoices_sum = (new \yii\db\Query())
//        ->select('sum(value) as money')
//        ->from('calc_invoicestud')
//		->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
//        ->one();
//
//		// получаем информацию по оплатам
//		$payments_sum = (new \yii\db\Query())
//        ->select('sum(value) as money')
//        ->from('calc_moneystud')
//		->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
//        ->one();
//
//        $model = Student::findOne($id);
//        $model->invoice = round($invoices_sum['money']);
//        $model->money = round($payments_sum['money']);
//        $model->save();
//		// считаем разницу как базовый долг
//		$debt_common = $payments_sum['money'] - $invoices_sum['money'];
//
//		// запрашиваем услуги назначенные студенту
//		$services = (new \yii\db\Query())
//		->select('s.id as sid, s.name as sname, SUM(is.num) as num')
//		->distinct()
//		->from('calc_service s')
//		->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
//		->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])
//		->andWhere(['is.calc_studname'=>$id])
//		->groupby(['is.calc_studname','s.id'])
//		->orderby(['s.id'=>SORT_ASC])
//		->all();
//
//		// проверяем что у студента есть назначенные услуги
//		if(!empty($services)){
//			$i = 0;
//			// распечатываем массив
//			foreach($services as $service){
//				// запрашиваем из базы колич пройденных уроков
//				$lessons = (new \yii\db\Query())
//				->select('COUNT(sjg.id) AS cnt')
//				->from('calc_studjournalgroup sjg')
//				->leftjoin('calc_groupteacher gt', 'sjg.calc_groupteacher=gt.id')
//				->leftjoin('calc_journalgroup jg', 'sjg.calc_journalgroup=jg.id')
//				->where('jg.view=:vis and jg.visible=:vis and (sjg.calc_statusjournal=:vis or sjg.calc_statusjournal=:stat) and gt.calc_service=:sid and sjg.calc_studname=:stid', [':vis'=>1, 'stat'=>3, ':sid'=>$service['sid'], ':stid'=>$id])
//				->one();
//
//				// считаем остаток уроков
//				$services[$i]['num'] = $services[$i]['num'] - $lessons['cnt'];
//				$i++;
//			}
//			// уничтожаем переменные
//			unset($service);
//			unset($lessons);
//			$arr = [];
//			foreach($services as $s) {
//                            if($s['num'] < 0){
//                                $lesson_cost = (new \yii\db\Query())
//                                ->select('(value/num) as money')
//                                ->from('calc_invoicestud')
//                                ->where('visible=:vis and calc_studname=:stid and calc_service=:sid', [':vis'=>1, ':stid'=>$id, ':sid'=>$s['sid']])
//                                ->orderby(['id'=>SORT_DESC])
//                                ->one();
//
//                                $debt_lessons = $debt_lessons + $s['num'] * $lesson_cost['money'];
//			    }
//			}
//		}
//		unset($services);
//		$debt = $debt_common + $debt_lessons;
//		/*if($debt < 0) {
//			$debt = ceil($debt);
//			if($debt == -0) {
//				$debt = 0;
//			}
//		} elseif($debt > 0) {
//			$debt = floor($debt);
//		}*/
//		//$debt = number_format($debt, 1, '.', ' ');
//		return round($debt);
//	}
}
