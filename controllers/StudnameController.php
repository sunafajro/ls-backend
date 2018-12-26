<?php

namespace app\controllers;

use Yii;
use app\models\Student;
use app\models\ClientAccess;
use app\models\Invoicestud;
use app\models\Moneystud;
use app\models\Office;
use app\models\Salestud;
use app\models\Schedule;
use app\models\StudentMergeForm;
use app\models\Studphone;
use app\models\Tool;
use app\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;
/*
 * StudnameController implements the CRUD actions for CalcStudname model.
 */
class StudnameController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'detail', 'active', 'inactive', 'merge', 'change-office'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'detail', 'active', 'inactive', 'merge', 'change-office'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'detail', 'active', 'inactive', 'merge', 'change-office'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
	{
		if(parent::beforeAction($action)) {
			if (User::checkAccess($action->controller->id, $action->id) == false) {
				throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
			}
			return true;
		} else {
			return false;
		}
	}

    /**
     * Lists all CalcStudname models.
     * @return mixed
     */
    public function actionIndex()
    {
        // $tss = NULL;
        // проверяем GET запрос на наличие переменной TSS (фильтр по имени)
        //if (Yii::$app->request->get('TSS')) {
        //    $tss = Yii::$app->request->get('TSS');
        //}

        // по умолчанию поиск по имени
        $tss = Yii::$app->request->get('TSS') ? Yii::$app->request->get('TSS') : NULL;
        $tss_condition = ['like', 's.name', $tss];
        // если в строке целое число, то поиск по идентификатору
        if ((int)$tss > 0) {
           $tss_condition = ['like', 's.phone', $tss];
        }

        $oid = NULL;
        // проверяем GET запрос на наличие переменной OID (фильтр по имени)
        if (Yii::$app->request->get('OID')) {
            if(Yii::$app->request->get('OID')=='all'){
                $oid = NULL;
            } else {
                $oid = Yii::$app->request->get('OID');
            }
        } else {
            if ((int)Yii::$app->session->get('user.ustatus') === 4) {
                $oid = (int)Yii::$app->session->get('user.uoffice_id');
            }
        }

        $state = 1;
        $state_id = 1;
        // проверяем GET запрос на наличие переменной STATE (фильтр по состоянию клиента)
        if (Yii::$app->request->get('STATE')) {
            if (Yii::$app->request->get('STATE')!='all') {
                switch(Yii::$app->request->get('STATE')){
                    // студент со статусом "С НАМИ"
                    case 1: $state_id = 1; break;
                    // студент со статусом "НЕ С НАМИ"
                    case 2: $state_id = 0; break;
                }
                $state = (int)Yii::$app->request->get('STATE');
            } else {
                $state = 'all';
                $state_id = NULL;
            }
        }

        // для руководителя и менеджера выводим полный список студентов
        if ((int)Yii::$app->session->get('user.ustatus') === 3 ||
        (int)Yii::$app->session->get('user.ustatus') === 4 ||
        (int)Yii::$app->session->get('user.uid') === 296) {
            // формируем запрос
            $students = (new \yii\db\Query())
            ->select([
                'stid' => 's.id',
                'stname' => 's.name',
                'visible' => 's.visible',
                'stphone' => 's.phone',
                'description' => 's.description',
                'stinvoice' => 's.invoice',
                'stmoney' => 's.money',
                'debt' => 's.debt',
                'stsex' => 's.calc_sex',
                'active' => 's.active'
            ])
            ->from(['s' => 'calc_studname']);
            if ($oid) {
                $students = $students->innerJoin('calc_student_office so', 'so.student_id=s.id');
            }
            $students = $students->where(['s.visible' => 1])
            ->andFilterWhere(['s.active' => $state_id])
            ->andFilterWhere($tss_condition);
            if ($oid) {
                $students = $students->andFilterWhere(['so.office_id' => $oid]);
            }
            // делаем клон запроса
            $countQuery = clone $students;
            // получаем данные для паджинации
            $pages = new Pagination(['totalCount' => $countQuery->count()]);

            $limit = 20;
            $offset = 0;
            if(Yii::$app->request->get('page')){
                if(Yii::$app->request->get('page') > 1 && Yii::$app->request->get('page') <= $pages->totalCount){
                    $offset = 20 * (Yii::$app->request->get('page') - 1);
                }
            }
            // доделываем запрос и выполняем
            $students = $students->orderBy(['s.active' => SORT_DESC, 's.name' => SORT_ASC])->limit($limit)->offset($offset)->all();
        } else {
            // формируем запрос
            $students = (new \yii\db\Query())
            ->select('cst.id as stid, cst.name as stname, cst.visible as visible, cst.phone as stphone, cst.description as description, cst.invoice as stinvoice, cst.money as stmoney, cst.debt2 as debt, cst.calc_sex as stsex, cst.active as active')
            ->distinct()
            ->from('calc_studname cst')
            ->leftjoin('calc_studgroup sg', 'sg.calc_studname=cst.id')
            ->leftjoin('calc_teachergroup tg', 'tg.calc_groupteacher=sg.calc_groupteacher')
            ->where('cst.visible=:vis and tg.calc_teacher=:tid', [':vis'=> 1, ':tid'=>Yii::$app->session->get('user.uteacher')])
            ->andFilterWhere(['cst.active'=>$state_id])
            ->andFilterWhere([$tss_condition]);
            // делаем клон запроса
            $countQuery = clone $students;
            // получаем данные для паджинации
            $pages = new Pagination(['totalCount' => $countQuery->count()]);

            $limit = 20;
            $offset = 0;
            if(Yii::$app->request->get('page')){
                if(Yii::$app->request->get('page')>1&&Yii::$app->request->get('page')<=$pages->totalCount){
                    $offset = 20 * (Yii::$app->request->get('page') - 1);
                }
            }
            // доделываем запрос и выполняем
            $students = $students->orderBy(['cst.name'=>SORT_ASC])->limit($limit)->offset($offset)->all();
        }
		// задаем переменную которая будет ключами для массива
		$i = 0;
		// задаем пустой массив
		$studentids = [];
		// распечатываем массив со студентами
		foreach($students as $student){
			// заполняем пустой массив id-шниками студентов
			$studentids[$i] = $student['stid'];
			//$students[$i]['debt'] = number_format($this->studentDebt($student['stid']), 1, '.', ' ');
			// увеличиваем переменную
			$i++;
		}
		// зададим пустой массив для услуг студента
		$services = [];
			// задаем пустой массив с телефонами
			$phones = [];
		// проверяем что выборка студентов не пустая
		if(!empty($studentids)){
            // запрашиваем услуги назначенные студенту
            $services = (new \yii\db\Query())
            ->select('s.id as sid, s.name as sname, is.calc_studname as stid, SUM(is.num) as num')
            ->distinct()
            ->from('calc_service s')
            ->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
            ->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])
            ->andWhere(['in','is.calc_studname',$studentids])
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
                    ->where('jg.view=:vis and jg.visible=:vis and (sjg.calc_statusjournal=:vis or sjg.calc_statusjournal=:stat) and gt.calc_service=:sid and sjg.calc_studname=:stid', [':vis'=>1, 'stat'=>3, ':sid'=>$service['sid'], ':stid'=>$service['stid']])
                    ->one();
                    // считаем остаток уроков
                    $services[$i]['num'] = $services[$i]['num'] - $lessons['cnt'];
                    $i++;
                }
                unset($service);
                unset($lessons);
            }
            // выбираем телефоны клиента
            $phones = (new \yii\db\Query())
            ->select('calc_studname as sid, phone as phone, description as description')
            ->from('calc_studphone')
            ->where('visible=:vis', [':vis'=>1])
            ->andWhere(['in','calc_studname', $studentids])
            ->all();
        }
        // получаем список офисов
        $offices = (new \yii\db\Query())
        ->select('id as oid, name as oname')
        ->from('calc_office')
        ->where('visible=1')
        ->all();
        // получаем список офисов

        // выводим данные в представление
        return $this->render('index', [
            'students' => $students,
            'services' => $services,
            'phones' => $phones,
            'pages' => $pages,
            'oid' => $oid,
            'tss' => $tss,
            'state' => $state,
            'offices' => $offices,
            'userInfoBlock' => User::getUserInfoBlock()
        ]);
    }

    /**
     * Метод позволяет вывести карточку клиента. Необходим ID клиента.
     * Преподавателям виден баланс пклиента и группы в которые он зачислен.
     * Менеджеры и руководители видят полную информацию по клиенту.
     */
    public function actionView($id)
    {
        $userInfoBlock = User::getUserInfoBlock();

        // проверяем какие данные выводить в карочку преподавателя: 1 - активные группы, 2 - завершенные группы, 3 - счета; 4 - оплаты
        if(Yii::$app->request->get('tab')){
            switch(Yii::$app->request->get('tab')){
                case 1: $tab = 1; $vis = 1; break;
                case 2: $tab = 2; $vis = 0; break;
                case 3: $tab = 3; break;
                case 4: $tab = 4; break;
            }
        } else {
            // для менеджеров и руководителей по умолчанию раздел счетов
            if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.ustatus') === 4) {
                $tab = 3;
            } else {
                // всем остальным раздел активных групп
                $tab = 1;
            }
            $vis = 1;
        }
        
        /* данные по назначенным скидкам и по колич оплаченных занятий видны только менеджерам и руководителям */
        if((int)Yii::$app->session->get('user.ustatus') === 3|| (int)Yii::$app->session->get('user.ustatus') === 4) {
            // список скидок клиента
            $studsales = Salestud::getAllClientSales($id);
				
			// постоянная скидка студента
			$permsale = Salestud::getClientPermamentSale($id);

            // расписание студента
            $schedule = Schedule::getStudentSchedule($id);

            // запрашиваем услуги назначенные студенту
            $services = (new \yii\db\Query())
            ->select('s.id as sid, s.name as sname, SUM(is.num) as num')
            ->distinct()
            ->from('calc_service s')
            ->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
            ->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])
            ->andWhere(['is.calc_studname' => $id])
            ->groupby(['is.calc_studname','s.id'])
            ->orderby(['s.id'=>SORT_ASC])
            ->all();
         
            // проверяем что у студента есть назначенные услуги
            if (!empty($services)) {
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
                    $cnt = $services[$i]['num'] - $lessons['cnt'];
                    $services[$i]['num'] = $cnt;
                    $services[$i]['npd'] = Moneystud::getNextPaymentDay($schedule, $service['sid'], $cnt);
                    $i++;
                }
                unset($service);
                unset($lessons);
            }
        } else {
            $studsales = [];
            $permsale = [];
            $services = [];
            $schedule = [];
        }
        
        /* вкладка Счета */
        if($tab == 3) {
            $invoices = Invoicestud::getStudentInvoiceById($id);
            
            $invcount = [1 => 0, 2 => 0, 3 => 0];
            foreach($invoices as $in) {
                if($in['idone']==0&&$in['ivisible']==1){
                    $invcount[1] = $invcount[1] + 1;
                }
                if($in['idone']==1&&$in['ivisible']==1){
                    $invcount[2] = $invcount[2] + 1;
                }
                if($in['ivisible']==0){
                    $invcount[3] = $invcount[3] + 1;
                }
            }
        } else {
            // если другая вкладка, создаем пустой массив
            $invoices = [];
            $invcount = [];
        }
        /* вкладка Счета */

        /* вкладка Оплаты */
        if($tab == 4) {
            // получаем оплаты пользователя
            $payments = Moneystud::getStudentPaymentById($id);

            // считаем года для группировки оплат
            $y = 0;
            $syears = array();
            foreach($payments as $pay){
                $syears[$y] = substr($pay['pdate'],0,7);
                $y++;
            }
            $years = array_unique($syears);
        } else {
            // если другая вкладка, создаем пустой массив
            $payments = [];
            $years = [];
        }
        /* вкладка Оплаты */

        // если нужны группы
        if($tab==1 || $tab == 2) {
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

        return $this->render('view', [
            'model'         => $this->findModel($id),
            'invoices'      => $invoices,
            'payments'      => $payments,
            'groups'        => $groups,
            'lessons'       => $lessons,
            'studsales'     => $studsales,
            'services'      => $services,
            'schedule'      => $schedule,
            'phones'        => Studphone::getStudentPhoneById($id),
            'years'         => $years,
            'invcount'      => $invcount,
            'clientaccess'  => ClientAccess::find()->where(['calc_studname'=>$id])->one(),
            'permsale'      => $permsale,
            'userInfoBlock' => $userInfoBlock,
            'offices'       => [
                'added' => Student::getStudentOffices($id),
                'all' => Office::getOfficesList(),
            ]
            //'debt'=>number_format($this->studentDebt($id), 1, '.', ' '),
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
        
        foreach($sexes as $s){
            $sex[$s['id']] = $s['name'];
        }
        unset($s);
        unset($sexes);

        $ways = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_way')
        ->all();

       foreach($ways as $w){
            $way[$w['id']] = $w['name'];
        }
        unset($w);
        unset($ways);


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
                'userInfoBlock' => $userInfoBlock
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

        foreach($sexes as $s){
            $sex[$s['id']] = $s['name'];
        }
        unset($s);
        unset($sexes);

        $ways = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_way')
        ->all();

       foreach($ways as $w){
            $way[$w['id']] = $w['name'];
        }
        unset($w);
        unset($ways);

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

            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'sex' => $sex,
                'way' => $way,
                'userInfoBlock' => $userInfoBlock
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


    public function actionDetail($id = null)
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                "status" => true,
                "detailsData" => [
                    "columns" => Student::getStudentDetailsColumns(),
                    "rows" => Student::getStudentDetailsRows($id)
                ]
            ];
        } else {
            return $this->render('detail', [
              'model' => $this->findModel($id)
            ]);
        }
    }

    public function actionMerge($id)
    {
        $userInfoBlock = User::getUserInfoBlock();
        $student = $this->findModel($id);
        $log = NULL;
        $model = new StudentMergeForm();

        if(Yii::$app->request->post()) {
            if((int)Yii::$app->request->post('StudentMergeForm')['id2'] > 0) {
                $id2 = (int)Yii::$app->request->post('StudentMergeForm')['id2'];
                if($id != $id2) {
                    $log = Student::mergeStudentAccounts($id, $id2);
                    $account2 = $this->findModel($id2);

                    $student->invoice = Student::getStudentTotalInvoicesSum($id);
                    $student->money = Student::getStudentTotalPaymentsSum($id);
                    $student->debt = $student->money - $student->invoice;
                    $student->description .= $account2->description;
                    $student->save();
                    
                    $account2->invoice = 0;
                    $account2->money = 0;
                    $account2->debt = 0;
                    $account2->visible = 0;
                    $account2->description = 'Данные перенесены в уч. запись №' . $id;
                    $account2->save();
                    
                    Yii::$app->session->setFlash('success', 'Процесс переноса данных прошел успешно!');
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
            'userInfoBlock' => $userInfoBlock
        ]);
    }

    public function actionChangeOffice($sid, $oid = 0, $action)
    {
        if (Yii::$app->request->isPost) {
            if ($action === 'add') {
                $db = (new \yii\db\Query())
                ->createCommand()
                ->insert('calc_student_office',
                [
                    'student_id' => $sid,
                    'office_id' => Yii::$app->request->post('office'),
                ])
                ->execute();
                $this->redirect(['studname/view', 'id' => $sid]);
            } else if ($action === 'delete') {
                $db = (new \yii\db\Query())
                ->createCommand()
                ->delete('calc_student_office', 'student_id=:sid AND office_id=:oid', [':sid' => $sid, ':oid' => $oid])->execute();
                $this->redirect(['studname/view', 'id' => $sid]);
            }
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    //public function actionCalculate($id)
    //{
        /*
        if(!Yii::$app->request->get('sid')) {
		$db = (new \yii\db\Query)
		->select('id')
		->from('calc_studname')
		->where('visible=:vis', [':vis'=>1])
		->limit(500)
		->offset(Yii::$app->request->get('l'))
		->all();
		
		foreach($db as $d) {
			$model = $this->findModel($d['id']);
			$model->debt2 = $this->studentDebt($model->id);
			$model->save();
		}
		unset($db2);
		unset($model);
		return $this->redirect(['index']);
        }
        //return $this->studentDebt(Yii::$app->request->get('sid'));
        */
        //$model = $this->findModel($id);
        //$model->debt2 = $this->studentDebt($model->id);
        //$model->save();
        //return $this->redirect(['studname/view','id'=>$id]);
    //}		
  
    /**
     * Finds the CalcStudname model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcStudname the loaded model
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
		
        $model = Student::findOne($id);
        $model->invoice = round($invoices_sum['money']);
        $model->money = round($payments_sum['money']);
        $model->save();
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
			$arr = [];
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
		/*if($debt < 0) {
			$debt = ceil($debt);
			if($debt == -0) {
				$debt = 0;
			}
		} elseif($debt > 0) {
			$debt = floor($debt);
		}*/
		//$debt = number_format($debt, 1, '.', ' ');
		return round($debt);
	}
}
