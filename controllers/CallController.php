<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Call;
use app\models\Student;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CallController implements the CRUD actions for CalcCall model.
 */
class CallController extends Controller
{
    public function behaviors()
    {
        return [
	    'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view','create','update','delete','transform','disable', 'ajaxgroup', 'autocomplete'],
                'rules' => [
                    [
                        'actions' => ['index','view','create','update','delete','transform','disable', 'ajaxgroup', 'autocomplete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index','view','create','update','transform','disable', 'ajaxgroup', 'autocomplete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                     [
                        'actions' => ['delete'],
                        'allow' => false,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CalcCall models.
     * @return mixed
     */
    public function actionIndex()
    {
        $filter = [];
        // задаем переменные для использования при фильтрации выборки
        $lid = NULL;
        $stid = NULL;
        $oid = NULL;
        $aid = NULL;
        // по дефолту задаем текущий месяц для выборки
        $month = date('n');
        // по дефолту задаем текущий год для выборки
        $year = date('Y');

        // проверяем GET запрос на наличие переменной TSS (фильтр по имени)
        if(Yii::$app->request->get('TSS')&&Yii::$app->request->get('TSS')!=''){
            $tss = Yii::$app->request->get('TSS');
        } else {
            $tss = NULL;
        }
        $filter['tss'] = $tss;

        // проверяем передан ли язык в GET
        if(Yii::$app->request->get('LID') && Yii::$app->request->get('LID')!='all'){
            $lid = Yii::$app->request->get('LID');
        }
        $filter['lid'] = $lid;
        // проверяем передана ли услуга в GET
        if(Yii::$app->request->get('STID') && Yii::$app->request->get('STID')!='all'){
            $stid = Yii::$app->request->get('STID');
        }
        $filter['stid'] = $stid;

        // проверяем передан ли номер офиса в GET
        if(Yii::$app->request->get('OID') && Yii::$app->request->get('OID')!='all'){
            $oid = Yii::$app->request->get('OID');
        }
        $filter['oid'] = $oid;

        // проверяем передан ли номер офиса в GET
        if(Yii::$app->request->get('AID') && Yii::$app->request->get('AID')!='all'){
            $aid = Yii::$app->request->get('AID');
        }
        $filter['aid'] = $aid;

        // проверяем передан ли месяц в GET
	    if(Yii::$app->request->get('month')){
            // если передан, не равен текущему и от 1 до 12
	        if(Yii::$app->request->get('month')>0 && Yii::$app->request->get('month')<13 && Yii::$app->request->get('mon')!=$month){
                // переприсваиваем переменную 
                $month = Yii::$app->request->get('month');
            }
            // если запрос выборки по всем месяцам 
	        elseif(Yii::$app->request->get('month')=='all'){
                $month = NULL;
            }
        }
        $filter['month'] = $month;

        // проверяем передан ли год в GET
        if (Yii::$app->request->get('year')) {
            // если передан, не равен текущему и от 2012 до текущего
	        if (Yii::$app->request->get('year')>2011 && Yii::$app->request->get('year')<($year+1) && Yii::$app->request->get('year')!=$year) {
                $year = Yii::$app->request->get('year');
	        } else if (Yii::$app->request->get('year')=='all') {
                $year = NULL;
            }
        }
        $filter['year'] = $year;

        $uid = NULL;

        if(Yii::$app->session->get('user.ustatus')==5){
            $uid = Yii::$app->session->get('user.uid');
        }

        // формируем запрос на выборку звонков
        $calls = (new \yii\db\Query())
        ->select('cc.id as cid, cc.name as cname, cc.phone as cphone, cst.name stname, cl.name as lname, cel.name as elname, cef.name as efname, co.name as oname, cc.description as cdesc, u.id as uid, u.name as uname, cc.data as cdate, cc.visible as cvisible, cc.calc_studname as stid, ea.name as eduage')
        ->from('calc_call cc')
        ->leftjoin('calc_servicetype cst', 'cst.id=cc.calc_servicetype')
        ->leftjoin('calc_lang cl', 'cl.id=cc.calc_lang')
        ->leftjoin('calc_edulevel cel', 'cel.id=cc.calc_edulevel')
        ->leftjoin('calc_eduform cef', 'cef.id=cc.calc_eduform')
        ->leftjoin('calc_office co', 'co.id=cc.calc_office')
        ->leftjoin('calc_eduage ea', 'ea.id=cc.calc_eduage')
        ->leftjoin('user u', 'u.id=cc.user')
        //->where('cc.visible=:vis', [':vis'=>1])
        ->andFilterWhere(['like', 'cc.name', $tss])
        ->andFilterWhere(['cc.user' => $uid])
        ->andFilterWhere(['month(cc.data)' => $month])
        ->andFilterWhere(['year(cc.data)' => $year])
        ->andFilterWhere(['cc.calc_lang' => $lid])
        ->andFilterWhere(['cc.calc_servicetype' => $stid])
        ->andFilterWhere(['cc.calc_office' => $oid])
        ->andFilterWhere(['cc.calc_eduage' => $aid])
        ->orderby(['cc.id'=>SORT_DESC])
        ->all();

        // делаем выборку языков для селектов
        $languages = (new \yii\db\Query())
        ->select('cl.id as lid, cl.name as lname')
        ->from('calc_lang cl')
        ->where('cl.visible=:vis and cl.id!=:wtht',[':vis'=>1,':wtht'=>16])
        ->orderby(['cl.name'=>SORT_ASC])
        ->all();

        // делаем выборку типов услуг для селектов
        $servicetypes = (new \yii\db\Query())
        ->select('cst.id as stid, cst.name as stname')
        ->from('calc_servicetype cst')
        ->where('cst.visible=:vis',[':vis'=>1])
        ->orderby(['cst.name'=>SORT_ASC])
        ->all();
        
		// делаем выборку офисов для селектов
        $offices = (new \yii\db\Query())
        ->select('o.id as oid, o.name as oname')
        ->from('calc_office o')
        ->where('o.visible=:vis',[':vis'=>1])
        ->orderby(['o.name'=>SORT_ASC])
        ->all();
		// делаем выборку офисов для селектов

		// делаем выборку офисов для селектов
        $ages = (new \yii\db\Query())
        ->select('a.id as aid, a.name as aname')
        ->from('calc_eduage a')
        ->where('a.visible=:vis',[':vis'=>1])
        ->orderby(['a.name'=>SORT_ASC])
        ->all();
		// делаем выборку офисов для селектов

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
			
        // передаем все во вьюз
        return $this->render('index', [
		'calls' => $calls,
		'languages' => $languages,
		'servicetypes' => $servicetypes,
        'offices' => $offices,
        'ages' => $ages,
        'months' => $months,
        'filter' => $filter,
        'userInfoBlock' => User::getUserInfoBlock()
        ]);
    }

    /**
     * Displays a single CalcCall model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CalcCall model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Call();
        // если звонок создается для уже имеющегося в базе клиента
        if(Yii::$app->request->get('sid')){
            $sid = Yii::$app->request->get('sid');
            // достаем данные по клиенту
            if(($client = Student::findOne($sid)) !== NULL){
                // заполняем нужные поля
                $model->name = $client->name;
                $model->phone = $client->phone;
                $model->email = $client->email;
                $model->calc_sex = $client->calc_sex;
                // тип привлечения клиента - Студент ШИЯ
                $model->calc_way = 13;
                $model->calc_studname = $client->id;
            }
        }

        $ways = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_way')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $way = [];
        foreach($ways as $w){
            $way[$w['id']] = $w['name'];
        }
        unset($ways);

        $servicetypes = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_servicetype')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $servicetype = [];
        foreach($servicetypes as $st){
            $servicetype[$st['id']] = $st['name'];
        }
        unset($st);
        unset($servicetypes);

        $languages = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_lang')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $language = [];
        foreach($languages as $l){
            $language[$l['id']] = $l['name'];
        }
        unset($l);
        unset($languages);

        $levels = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_edulevel')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $level = [];
        foreach($levels as $ls){
            $level[$ls['id']] = $ls['name'];
        }
        unset($ls);
        unset($levels);

        $ages = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_eduage')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $age = [];
        foreach($ages as $a){
            $age[$a['id']] = $a['name'];
        }
        unset($a);
        unset($ages);

        $eduforms = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_eduform')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $eduform = [];
        foreach($eduforms as $f){
            $eduform[$f['id']] = $f['name'];
        }
        unset($f);
        unset($eduforms);

        $offices = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_office')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $office = [];
        foreach($offices as $o){
            $office[$o['id']] = $o['name'];
        }
        unset($o);
        unset($offices);
        
		/*
        $students = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name', 'phone'=>'phone'])
        ->from('calc_studname')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $student = [];
        foreach($students as $stn){
            $student[$stn['id']] = $stn['name']." (#".$stn['id'].", ".$stn['phone'].")";
        }
        unset($stn);
        unset($students);
		
		$studdata=Student::find()
        ->select(['name as  label', 'id as value'])
		->asArray()
        ->all();
		*/
        // если данные пришли в POST запросе и успешно залились в модель
        if ($model->load(Yii::$app->request->post())) {
            // добавляем в модель данные о пользователе и дате создания
            $model->user = Yii::$app->session->get('user.uid');
            $model->data = date('Y-m-d H:i:s');
            // ставим отметку, что запись о звонка действующая
            $model->visible = 1;
            /*
            // если звонок добавляет менеджер или руководитель
            if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
                // ставим помеку о проверке
                $model->flag_check = 1;
                // указываем id проверившего пользователя
                $model->user_check = Yii::$app->session->get('user.uid');
                // и дату проверки
                $model->data_check = $model->data;
            } else {
                // ставим помеку, что звонок не проверен
                $model->flag_check = 0;
                $model->user_check = 0;
                $model->data_check = '0000-00-00 00:00:00';
            }
            */
            // проверяем указан ли id клиента в записи о звонке
            if($model->calc_studname!=NULL){
                // помечаем соответствующее значение, что карточка клиента существует
                $model->transform = 1;
            } else {
                // помечаем соответствующее значение, что у клиента еще нет карточки
                $model->transform = 0;
                $model->calc_studname = 0;
            }
			
			if($model->calc_edulevel==NULL) {
                $model->calc_edulevel = 0;
            }
			
            if($model->calc_eduform==NULL) {
                $model->calc_eduform = 0;
            }

            if($model->calc_office==NULL) { 
                if(Yii::$app->session->get('user.ustatus')==4) {
                    $model->calc_office = Yii::$app->session->get('user.uoffice_id');
                } else {
                    $model->calc_office = 0;
                }
            }
			//var_dump($model);die();
            // сохраняем модель
            if($model->save()){
				Yii::$app->session->setFlash('success', \Yii::t('app','Call successfully added!'));
			} else {
				Yii::$app->session->setFlash('error', \Yii::t('app','Call not added!'));
			}
            // возвращаемся в список звонков
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'way' => $way,
                'servicetype' => $servicetype,
                'language' => $language,
                'level' => $level,
                'age' => $age,
                'eduform' => $eduform,
                'office' => $office,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * Updates an existing CalcCall model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $ways = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_way')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $way = [];
        foreach($ways as $w){
            $way[$w['id']] = $w['name'];
        }
        unset($w);
        unset($ways);

        $servicetypes = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_servicetype')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $servicetype = [];
        foreach($servicetypes as $st){
            $servicetype[$st['id']] = $st['name'];
        }
        unset($st);
        unset($servicetypes);

        $languages = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_lang')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $language = [];
        foreach($languages as $l){
            $language[$l['id']] = $l['name'];
        }
        unset($l);
        unset($languages);

        $levels = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_edulevel')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $level = [];
        foreach($levels as $ls){
            $level[$ls['id']] = $ls['name'];
        }
        unset($ls);
        unset($levels);

        $ages = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_eduage')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $age = [];
        foreach($ages as $a){
            $age[$a['id']] = $a['name'];
        }
        unset($a);
        unset($ages);

        $eduforms = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_eduform')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $eduform = [];
        foreach($eduforms as $f){
            $eduform[$f['id']] = $f['name'];
        }
        unset($f);
        unset($eduforms);

        $offices = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name'])
        ->from('calc_office')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $office = [];
        foreach($offices as $o){
            $office[$o['id']] = $o['name'];
        }
        unset($o);
        unset($offices);

        $students = (new \yii\db\Query())
        ->select(['id'=>'id', 'name'=>'name', 'phone'=>'phone'])
        ->from('calc_studname')
        ->where('visible=:vis', [':vis'=>1])
        ->orderby(['name'=>SORT_ASC])
        ->all();

        $student = [];
        foreach($students as $stn){
            $student[$stn['id']] = $stn['name']." (#".$stn['id'].", ".$stn['phone'].")";
        }
        unset($stn);
        unset($students);

       $services = (new \yii\db\Query())
       ->select('id as id, name as name')
       ->from('calc_service')
       ->where('visible=:vis and calc_servicetype=:type and data>:data', [':vis'=>1, ':type'=>$model->calc_servicetype, ':data'=>date('Y-m-d')])
       ->orderBy(['id'=>SORT_ASC])
       ->all();

        $service = [];
        foreach($services as $s){
            $service[$s['id']] = $s['name'];
        }
        unset($s);
        unset($services);

        if ($model->load(Yii::$app->request->post())) {
            // добавляем данные о пользователе и дате редактиорования
            $model->user_edit = Yii::$app->session->get('user.uid');
            $model->data_edit = date('Y-m-d H:i:s');
            // если звонок редактирует менеджер или руководитель
            if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
                // ставим помеку о проверке
                $model->flag_check = 1;
                // указываем id проверившего пользователя
                $model->user_check = Yii::$app->session->get('user.uid');
                // и дату проверки
                $model->data_check = $model->data;
            }

            if($model->calc_studname==0&&$model->transform==1){
                $model->transform = 0;
            }
            elseif($model->calc_studname==1&&$model->transform==0){
                $model->transform = 1;
            }
			if($model->calc_servicetype != 1) {
				$model->calc_eduform = 0;
				$model->calc_office = 0;
			}
            $model->save();
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'way' => $way,
                'servicetype' => $servicetype,
                'language' => $language,
                'level' => $level,
                'age' => $age,
                'eduform' => $eduform,
                'office' => $office,
                'student' => $student,
                'service'=>$service,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * Deletes an existing CalcCall model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
	/*
	* метод позволяет создать из информации по звонку нового клиента
	*/
    public function actionTransform($id)
    {
        // проверяем роль пользователя, создание клиента разрешено только руководителям и менеджерам
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            // получаем данные по записи звонка
            $call = $this->findModel($id);
            // на всякий случай проверяем, что по звонку карточки клиента нет
            if($call->transform == 0) {
                // создаем модель клиента  
                $student = new Student();
                $student->name = $call->name;
                $student->phone = $call->phone;
                $student->calc_sex = $call->calc_sex;
                if($call->email){
                    $student->email = $call->email;
                }
                if($call->description){
                    $student->description = $call->description;
                }
                if($call->calc_way){
                    $student->calc_way = $call->calc_way;
                }
                $student->history = 0;
                $student->visible = 1;
                $student->active = 1;
                $student->debt = 0;
                $student->debt2 = 0;
                $student->invoice = 0;
                $student->money = 0;

                // сохраняем модель клиента
                if($student->save()){
                    // формируем связь студента с офисом
                    $db = (new \yii\db\Query())
                    ->createCommand()
                    ->insert('calc_student_office',
                    [
                        'student_id' => $student->id,
                        'office_id' => (int)Yii::$app->session->get('user.ustatus') === 4 ? Yii::$app->session->get('user.uoffice_id') : $call->calc_office,
                    ])
                    ->execute();
                    // обновляем данные записи звонка
                    $call->calc_studname = $student->id;
                    // указываем что запись о звонке трансформирована в карточку клиента
                    $call->transform = 1;
                    // указываем пользователя создавшего клиента
                    $call->user_transform = Yii::$app->session->get('user.uid');
                    // указываем дату создания
                    $call->data_transform = date('Y-m-d H:i:s');
                    // сохраняем измененные данные
                    $call->save();
                }

                // идем обратно в список звонков
                return $this->redirect(['studname/view','id'=>$student->id]);

            } else {
                // возвращаемся в список звонков
                return $this->redirect(['index']);
            }
        } else {
            // возвращаемся в список звонков
            return $this->redirect(['index']);
        }
    }

    public function actionDisable($id)
    {
        // проверяем, роль пользователя 3- руководитель, 4 - менеджер, 5 - преподаватель
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4||Yii::$app->session->get('user.ustatus')==5) {
            // получаем данные по записи звонка
            $call = $this->findModel($id);
            // проверяем, что запись о звонке действующая
            if($call->visible==1){
                // если пользователь - преподаватель, и звонок не его
                if(Yii::$app->session->get('user.ustatus')==5&&Yii::$app->session->get('user.uid')!=$call->user) {
                    // возвращаемся к списку звонков
                    return $this->redirect(['index']);
                } else {
                    // помечаем запись о звонке как удаленную
                    $call->visible = 0;
                    // добавляем информацию о пользователе и дате удаления
                    $call->user_visible = Yii::$app->session->get('user.uid');
                    $call->data_visible = date('Y-m-d H:i:s');

                    // сохраняем изменения
                    $call->save();

                    // возвращаемся в список звонков
                    return $this->redirect(['index']);
                    
                }
            } else {
            // возвращаемся в список звонков
            return $this->redirect(['index']);
            }            
        } else {
            // возвращаемся в список звонков
            return $this->redirect(['index']);
        }
    }

    public function actionAjaxgroup() 
    {
        // проверяем что запрос ajax
        if (Yii::$app->request->isAjax) {
            //достаем из базы и возвращаем селект с типами обучения
            if(Yii::$app->request->post('type')&&Yii::$app->request->post('type')==1&&Yii::$app->request->post('field')&&Yii::$app->request->post('field')==1) {
                $formgroups = (new \yii\db\Query())
                ->select('id as id, name as name')
                ->from('calc_eduform')
                ->where('visible=:vis', [':vis'=>1])
                ->orderBy(['id'=>SORT_ASC])
                ->all();
                $eduform = "<label class='control-label' for='call-calc_eduform'>".\Yii::t('app','Education type')."</label>"; 
                $eduform .= "<select id='call-calc_eduform' class='form-control' name='Call[calc_eduform]'>";
                $eduform .= "<option value=''>-выбрать-</option>";
                foreach($formgroups as $fg){
                    $eduform .= "<option value='".$fg['id']."'>".$fg['name']."</option>";
                }
                $eduform .= "</select>";
                $eduform .= "<div class='help-block'></div>";
            
                return $eduform;
            }

            //достаем из базы и возвращаем селект с офисами
            if(Yii::$app->request->post('type')&&Yii::$app->request->post('type')==1&&Yii::$app->request->post('field')&&Yii::$app->request->post('field')==2) {
                $offices = (new \yii\db\Query())
                ->select('id as id, name as name')
                ->from('calc_office')
                ->where('visible=:vis', [':vis'=>1])
                ->orderBy(['id'=>SORT_ASC])
                ->all();

                $office = "<label class='control-label' for='call-calc_office'>".\Yii::t('app','Office')."</label>"; 
                $office .= "<select id='call-calc_office' class='form-control' name='Call[calc_office]'>";
                $office .= "<option value=''>-выбрать-</option>";
                foreach($offices as $o){
                    $office .= "<option value='".$o['id']."'>".$o['name']."</option>";
                }
                $office .= "</select>";
                $office .= "<div class='help-block'></div>";
                
                return $office;
            }

            //достаем из базы и возвращаем селект с услугами
            if(Yii::$app->request->post('type')&&Yii::$app->request->post('type')>=1&&Yii::$app->request->post('type')!=6&&!Yii::$app->request->post('field')) {
                $services = (new \yii\db\Query())
                ->select('id as id, name as name')
                ->from('calc_service')
                ->where('visible=:vis and calc_servicetype=:type and data>:data', [':vis'=>1, ':type'=>Yii::$app->request->post('type'), ':data'=>date('Y-m-d')])
                ->orderBy(['id'=>SORT_ASC])
                ->all();

                $service = "<label class='control-label' for='call-calc_service'>".\Yii::t('app','Services')."</label>"; 
                $service .= "<select id='call-calc_service' class='form-control' name='Call[calc_service]'>";
                $service .= "<option value=''>-выбрать-</option>";
                foreach($services as $s){
                    $service .= "<option value='".$s['id']."'>#".$s['id']." ".$s['name']."</option>";
                }
                $service .= "</select>";
                $service .= "<div class='help-block'></div>";
                
                return $service;
            }
        }
    }
	
	public function actionAutocomplete()
    {    
        $term = Yii::$app->request->get('term');
        if ($term=='') $term='';
        
        $data = (new \yii\db\Query())
        ->select(['id as id', 'name as name', 'phone as phone'])
		->from('calc_studname')
        ->where('visible=:one', [':one' => 1])
        ->andWhere(['like', 'name', $term])
		->limit(8)
        ->all();

		$i = 0;
		$list = [];
		if(!empty($data)) {
		    foreach($data as $d){
			    $list[$i]['value'] = $d['id'];
				$list[$i]['label'] = "#".$d['id']." ".$d['name']." (".$d['phone'].")";
				$i++;
		    }
			unset($d);
			unset($data);
		}
        echo json_encode($list);
        
    }

    /**
     * Finds the CalcCall model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcCall the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Call::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
