<?php

namespace app\controllers;

use Yii;
use app\models\Eduage;
use app\models\Eduform;
use app\models\Office;
use app\models\Room;
use app\models\Schedule;
use app\models\Tool;
use app\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ScheduleController implements the CRUD actions for CalcSchedule model.
 */
class ScheduleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view','create','update','disable','hours','ajaxgroup'],
                'rules' => [
                    [
                        'actions' => ['index','create','update','disable','hours','ajaxgroup'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index','create','update','disable','hours','ajaxgroup'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CalcSchedule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $userInfoBlock = User::getUserInfoBlock();
        // задаем начальные переменные
		$oid = NULL;
		$lid = NULL;
		$eid = NULL;
		$aid = NULL;
		$tid = NULL;
		$day = date('N');
		
		// если у пользователя роль - менеджер
        if(Yii::$app->session->get('user.ustatus')==4) {
			// задаем идентификатор офиса
            $oid = Yii::$app->session->get('user.uoffice_id');
			// расписание на все дни
			$day = NULL;
        }
		
		// если у пользователя роль - преподаватель
        if((int)Yii::$app->session->get('user.ustatus')=== 5 && (int)Yii::$app->session->get('user.uid') !== 296) {
            // задаем идентификатор офиса
            $tid = Yii::$app->session->get('user.uteacher');
            // расписание на все дни
            $day = NULL;
        }
		
		// получаем номер дня недели из запроса или используем текущий (переприсваивает переменную выставленную для менеджера т.к. фильтр приоритетнее)
        if(Yii::$app->request->get('day')) {
            if((Yii::$app->request->get('day')>=1 && Yii::$app->request->get('day')<=7)){
                $day = Yii::$app->request->get('day');
            } else {
                $day = NULL;
            }
        }
		
        // задаем параметр фильтрации по офису (переприсваивает переменную выставленную для менеджера т.к. фильтр приоритетнее)
        if(Yii::$app->request->get('OID')&&Yii::$app->request->get('OID')!='all') {
            $oid = Yii::$app->request->get('OID');
        } elseif (Yii::$app->request->get('OID')=='all') {
			$oid = NULL;		
		}
		
        // задаем параметр фильтрации по языку
        if(Yii::$app->request->get('LID')&&Yii::$app->request->get('LID')!='all') {
            $lid = Yii::$app->request->get('LID');
        }
		
        // задаем параметр фильтрации по типу обучения
        if(Yii::$app->request->get('EID')&&Yii::$app->request->get('EID')!='all') {
            $eid = Yii::$app->request->get('EID');
        }
		
        // задаем параметр фильтрации по возрасту
        if(Yii::$app->request->get('AID')&&Yii::$app->request->get('AID')!='all') {
            $aid = Yii::$app->request->get('AID');
        }
		
        // задаем параметр фильтрации по преподавателю
        if(Yii::$app->request->get('TID')&&Yii::$app->request->get('TID')!='all') {
            $tid = Yii::$app->request->get('TID');
        } elseif (Yii::$app->request->get('TID')=='all') {
			$tid = NULL;		
		}
		
        // пишем исходный кусок запроса    
    	$lessons = (new \yii\db\Query()) 
        ->select('csch.id lessonid, co.name as office, cko.name as room, ctch.id as tid, ctch.name as teacher, cgt.id as gid, cdn.name as day, csch.calc_denned as day_id, csch.time_begin as start, csch.time_end as end, cs.name as service, csch.visible as visible')
        ->from('calc_schedule csch')
        ->leftJoin('calc_office co', 'co.id=csch.calc_office')
        ->leftJoin('calc_cabinetoffice cko', 'cko.id=csch.calc_cabinetoffice')
        ->leftJoin('calc_teacher ctch', 'ctch.id=csch.calc_teacher')
        ->leftJoin('calc_denned cdn', 'cdn.id=csch.calc_denned')
        ->leftJoin('calc_groupteacher cgt', 'cgt.id=csch.calc_groupteacher')
        ->leftJoin('calc_service cs', 'cs.id=cgt.calc_service')
        ->where('csch.calc_groupteacher!=:zero AND co.visible=:vis and csch.visible=:vis', [':zero'=>0, ':vis'=>1]);
        // добавляем условие выборки по номеру дня недели
        $lessons = $lessons->andFilterWhere(['cdn.id'=>$day]);
        // добавляем условия выборки по параметрам полученным из формы
        $lessons = $lessons->andFilterWhere(['csch.calc_office'=>$oid]);
        $lessons = $lessons->andFilterWhere(['cs.calc_lang'=>$lid]);
        $lessons = $lessons->andFilterWhere(['cs.calc_eduform'=>$eid]);
        $lessons = $lessons->andFilterWhere(['cs.calc_eduage'=>$aid]);
        $lessons = $lessons->andFilterWhere(['csch.calc_teacher'=>$tid]);
        // добавляем сортировку
        $lessons = $lessons->orderby(['co.id'=>SORT_ASC, 'csch.calc_denned'=>SORT_ASC,'cko.name'=>SORT_ASC, 'csch.time_begin'=>SORT_ASC])
	    // запрашиваем данные
        ->all();

        // получаем массив со списком номеров кабинетов 
    	$rooms = (new \yii\db\Query())
    	->select('name as rname')
    	->from('calc_cabinetoffice')
    	->orderBy(['name'=>SORT_ASC])
    	->all();

    	// получаем массив со списком офисов
    	$teacheroffices = (new \yii\db\Query())
    	->select('co.id as oid, co.name as oname, csch.calc_teacher as tid')
    	->from('calc_schedule csch')
    	->leftJoin('calc_office co', 'csch.calc_office=co.id')
    	->where('co.visible=:vis', [':vis'=>1])
        ->orderBy(['co.id'=>SORT_ASC])
        ->all();

        //составляем список офисов для селектов
        foreach($teacheroffices as $office){
            $tempoffices[$office['oid']]=$office['oname'];
        }
        $soffices = array_unique($tempoffices);

    	// получаем массив языков
    	$teacherlangs =  (new \yii\db\Query())
        ->select('cl.id as lid, cl.name as lname, clt.calc_teacher as tid')
        ->from('calc_lang cl')
        ->leftJoin('calc_langteacher clt', 'cl.id=clt.calc_lang')
        ->where('clt.visible=:vis', [':vis'=>1])
        ->orderBy(['cl.name'=>SORT_ASC,'clt.calc_teacher'=>SORT_ASC])
        ->all();

        //составляем список языков для селектов
        foreach($teacherlangs as $lang){
            $templangs[$lang['lid']]=$lang['lname'];
        }
        $slangs = array_unique($templangs);

        // получаем массив преподавателей
    	$steachers = (new \yii\db\Query())
    	->select('ctch.id as tid, ctch.name as tname')
    	->from('calc_schedule csch')
    	->leftJoin('calc_teacher ctch','csch.calc_teacher=ctch.id')
    	->orderBy(['ctch.name'=>SORT_ASC])
    	->all();

        //составляем список преподавателей для селектов
        foreach($steachers as $steacher){
            $tempteachers[$steacher['tid']]=$steacher['tname'];
        }
        $teachers = array_unique($tempteachers);
        if(!empty($lessons)) {
            $key = 0;
            // распечатываем в массив названия офисов
            foreach($lessons as $less){
                $offices[$key]=$less['office'];
                $key++;
            }
        } else {
            $offices = [];
        }

        // получаем массив преподавателей

        return $this->render('index', [
    	    'lessons' => $lessons,
            'offices' => $offices,
    	    'soffices' => $soffices,
    	    'slangs' => $slangs,
    	    'teachers' => $teachers,
            'oid' => $oid,
            'lid' => $lid,
            'eid' => $eid,
            'aid' => $aid,
            'tid' => $tid,
            'day' => $day,
            'days' => Tool::getDayOfWeekSimple(),
            'userInfoBlock' => $userInfoBlock
        ]);
    }

    /**
     * Метод позволяет преподавателям, менеджерам и руководителям 
     * создавать записи занятий в расписании.
     */
    public function actionCreate()
    {
        /* проверяем права доступа */
        if(!Yii::$app->session->get('user.ustatus')==3 && !Yii::$app->session->get('user.ustatus')==4 && !Yii::$app->session->get('user.uteacher')){
            return $this->redirect(Yii::$app->request->referrer);
        }
        
        $userInfoBlock = User::getUserInfoBlock();
        // создаем новую пустую запись
        $model = new Schedule();
        // определяем фильтр выборки
        if(Yii::$app->session->get('user.ustatus')==5){
            // для преподавателей
            $teacher = Yii::$app->session->get('user.uteacher');
        } else {
        //  для менеджеров и руководителей
            $teacher = NULL;
        }
        // получаем список преподавателей для селекта формы
        $steachers = (new \yii\db\Query())
        ->select('ctch.id as tid, ctch.name as tname')
        ->from('calc_teacher ctch')
        ->leftjoin('calc_teachergroup tg', 'tg.calc_teacher=ctch.id')
        ->leftJoin('calc_groupteacher cgt','cgt.id=tg.calc_groupteacher')
        ->where('ctch.visible=:vis and ctch.old=:old and cgt.visible=:vis',[':vis'=>1,':old'=>0])
        ->andFilterWhere(['ctch.id'=>$teacher])
        ->orderBy(['ctch.name'=>SORT_ASC])
        ->all();

        //составляем список преподавателей для селекта
        foreach($steachers as $steacher){
            $tempteachers[$steacher['tid']]=$steacher['tname'];
        }
        $teachers = array_unique($tempteachers);
        unset($tempteachers);
        unset($steacher);
        unset($steachers);

        // получаем список офисов в которых есть занятия для селекта формы
        $teacheroffices = (new \yii\db\Query())
        ->select('co.id as oid, co.name as oname')
        ->from('calc_office co')
        ->where('co.visible=:vis', [':vis'=>1])
        ->orderBy(['co.id'=>SORT_ASC])
        ->all();

        //составляем массив офисов для селекта
        foreach($teacheroffices as $teacheroffice){
            $offices[$teacheroffice['oid']]=$teacheroffice['oname'];
        }
        unset($teacheroffice);
        unset($teacheroffices);

        if ($model->load(Yii::$app->request->post())) {
            // присваиваем id преподавателя для всех кроме менеджеров и руководителей
            //if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            //   $model->calc_teacher = Yii::$app->session->get('user.uteacher');
            //}
            // помечаем занятие как действующее
            $model->visible = 1;
            // указываем пользователя добавившего занятие в расписание
            $model->user = Yii::$app->session->get('user.uid');
            // указывае дату добавления занятия в расписание
            $model->data = date('Y-m-d');
            // сохраняем запись
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Занятие успешно добавлено в расписание!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить занятие в расписание!');
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'offices' => $offices,
                'teachers' => $teachers,
                'days' => Tool::getDayOfWeekSimple(),
                'userInfoBlock' => $userInfoBlock
            ]);
        }
    }

    /**
     * Метод позволяет менеджерам, преподавателям и руководителям
     * изменять записии занятий в расписании.
     * Требуется ID записи.
     */
    public function actionUpdate($id)
    {
        // находим запись о занятии
        $model = $this->findModel($id);
        if(!Yii::$app->session->get('user.ustatus') ==3 && !Yii::$app->session->get('user.ustatus') == 4 && $model->calc_teacher != Yii::$app->session->get('user.uteacher')){
            return $this->redirect(Yii::$app->request->referrer);
        }
        
        $userInfoBlock = User::getUserInfoBlock();
        // определяем фильтр выборки
        if(Yii::$app->session->get('user.ustatus')==5){
            // для преподавателей
            $teacher = Yii::$app->session->get('user.uteacher');
        } else {
            //  для менеджеров и руководителей
            $teacher = NULL;
        }
        
        $steachers = (new \yii\db\Query())
        ->select('ctch.id as tid, ctch.name as tname')
        ->from('calc_teacher ctch')
        ->leftJoin('calc_groupteacher cgt','cgt.calc_teacher=ctch.id')
        ->where('ctch.visible=:vis and ctch.old=:old and cgt.visible=:vis',[':vis'=>1,':old'=>0])
        ->andFilterWhere(['ctch.id'=>$teacher])
        ->orderBy(['ctch.name'=>SORT_ASC])
        ->all();

        //составляем список преподавателей для селекта
        foreach($steachers as $steacher){
            $tempteachers[$steacher['tid']] = $steacher['tname'];
        }
        $teachers = array_unique($tempteachers);
        unset($tempteachers);
        unset($steacher);
        unset($steachers);

        $teacheroffices = (new \yii\db\Query())
        ->select('co.id as oid, co.name as oname')
        ->from('calc_office co')
        ->where('co.visible=:vis', [':vis'=>1])
        ->orderBy(['co.id'=>SORT_ASC])
        ->all();

        //составляем массив офисов для селекта
        foreach($teacheroffices as $teacheroffice){
            $offices[$teacheroffice['oid']]=$teacheroffice['oname'];
        }
        unset($teacheroffice);
        unset($teacheroffices);
        
        $teachgroups = (new \yii\db\Query())
        ->select('cgt.id as gid, cs.id as sid, cs.name as gname')
        ->from('calc_groupteacher cgt')
        ->leftJoin('calc_service cs','cs.id=cgt.calc_service')
        ->where('cgt.visible=:vis and cgt.calc_teacher=:tid',[':vis'=>1,':tid'=>$model->calc_teacher])
        ->all();
        
        //составляем массив групп для селекта
        foreach($teachgroups as $teachergroup){
            $groups[$teachergroup['gid']] = "#".$teachergroup['gid']." ".$teachergroup['gname'];
        }
        unset($teachergroup);
        unset($teachgroups);

        // выбираем список кабинетов
        $officecabinets = (new \yii\db\Query())
        ->select('cco.id as cid, co.id as oid, cco.name as cname')
        ->from('calc_cabinetoffice cco')
        ->leftJoin('calc_office co','co.id=cco.calc_office')
        ->where('cco.visible=:vis and co.visible=:vis and cco.calc_office=:oid',[':vis'=>1,':oid'=>$model->calc_office])
        ->all();

        //составляем массив кабинетов для селекта
        foreach($officecabinets as $officecabinet){
            $cabinets[$officecabinet['cid']]=$officecabinet['cname'];
        }
        unset($officecabinet);
        unset($officecabinets);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'offices'=>$offices,
                'teachers'=>$teachers,
                'groups'=>$groups,
                'cabinets'=>$cabinets,
                'days' => Tool::getDayOfWeekSimple(),
                'userInfoBlock' => $userInfoBlock
            ]);
        }
    }

    /**
     * Deletes an existing CalcSchedule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	/*
    public function actionDelete($id)
    {
		if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            $model = $this->findModel($id);
			$model->visible = 0;
			
		} else {
            return $this->redirect(['index']);
        }
    }
    */
    /*
    public function actionEnable($id)
    {
        // получаем информацию по пользователю
        $model=$this->findModel($id);
        // проверяем что пользователь руководитель или менеджер или преподаватель которому назначено занятие
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4||$model->calc_teacher==Yii::$app->session->get('user.uteacher')){
            //проверяем текущее состояние
            if($model->visible==0) {
                // помечаем занятие как действующее
                $model->visible = 1;
                // сохраняем модель
                $model->save();
            }
        }
        return $this->redirect(['index']);
    }
    */
    public function actionDisable($id)
    {
        // получаем информацию по пользователю
        $model=$this->findModel($id);
        // проверяем что пользователь руководитель или менеджер или преподаватель которому назначено занятие
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4||$model->calc_teacher==Yii::$app->session->get('user.uteacher')){
            //проверяем текущее состояние
            if($model->visible==1){
                // помечаем занятие как отмененное
                $model->visible = 0;
                // сохраняем модель
                $model->save();
            }
        }
        return $this->redirect(['index']);
    }

    public function actionHours()
    {
        /* проверяем права доступа */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $params['oid'] = (Yii::$app->request->get('OID') && Yii::$app->request->get('OID') !== 'all') ? Yii::$app->request->get('OID') : NULL;

        // пишем исходный кусок запроса    
    	$lessons = (new \yii\db\Query()) 
        ->select('sch.id as schedule_id,
        t.id as teacher_id,
        t.name as teacher,
        o.id as office_id,
        o.name as office,
        l.id as language_id,
        l.name as language,
        tn.value as hours')
        ->distinct()
        ->from('calc_schedule sch')
        ->innerJoin('calc_groupteacher gt', 'gt.id=sch.calc_groupteacher')
        ->innerJoin('calc_service s', 's.id=gt.calc_service')
        ->innerJoin('calc_timenorm tn', 's.calc_timenorm=tn.id')
        ->innerJoin('calc_lang l', 'l.id=s.calc_lang')
        ->innerJoin('calc_teachergroup tg', 'tg.calc_teacher=sch.calc_teacher')
        ->innerJoin('calc_teacher t', 't.id=sch.calc_teacher')
        ->innerJoin('calc_office o', 'o.id=sch.calc_office')
        ->where('sch.calc_groupteacher!=:zero AND o.visible=:vis and sch.visible=:vis', [':zero'=>0, ':vis'=>1])
        ->andFilterWhere(['sch.calc_office' => $params['oid']])
        ->orderby(['t.name'=>SORT_ASC, 'l.id'=>SORT_ASC])
        ->all();

        $teachers = [];
        if(!empty($lessons)) {
            foreach($lessons as $l) {
                $teachers[$l['teacher_id']] = $l['teacher'];
            }
            $teachers = array_unique($teachers);
        }

        $languages = [];
        if(!empty($lessons)) {
            foreach($lessons as $l) {
                $languages[$l['language_id']] = $l['language'];
            }
            $languages = array_unique($languages);
        }

        // получаем массив со списком офисов
    	$tmp_offices = (new \yii\db\Query())
    	->select('co.id as oid, co.name as oname, csch.calc_teacher as tid')
    	->from('calc_schedule csch')
    	->leftJoin('calc_office co', 'csch.calc_office=co.id')
    	->where('co.visible=:vis', [':vis'=>1])
        ->orderBy(['co.id'=>SORT_ASC])
        ->all();
        
        $offices = [];
        if (!empty($tmp_offices)) {
            //составляем список офисов для селектов
            foreach($tmp_offices as $o){
                $offices[$o['oid']]=$o['oname'];
            }

            $offices = array_unique($offices);
        }

        return $this->render('hours', [
            'params' => $params,
            'teachers' => $teachers,
            'languages' => $languages,
            'lessons' => $lessons,
            'offices' => $offices,
            'userInfoBlock' => User::getUserInfoBlock()
        ]);
    }

    /**
     * Finds the CalcSchedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcSchedule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Schedule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAjaxgroup() 
    {
        if (Yii::$app->request->isAjax) {
            //достаем из базы и возвращаем кусок кода для выбора уч.группы преподавателя.
            if(Yii::$app->request->post('TID')){
                $teachergroups = (new \yii\db\Query())
                ->select('gt.id as gid, s.id as sid, s.name as gname')
                ->from('calc_teachergroup tg')
                ->innerJoin('calc_groupteacher gt', 'tg.calc_groupteacher=gt.id')
                ->innerJoin('calc_service s','s.id=gt.calc_service')
                ->where('gt.visible=:vis and tg.calc_teacher=:tid', [':vis'=>1,':tid'=>Yii::$app->request->post('TID')])
                ->orderBy(['s.name'=>SORT_ASC])
                ->all();
                $teachergroup = "<label class='control-label' for='schedule-calc_groupteacher'>".\Yii::t('app','Group')."</label>"; 
                $teachergroup .= "<select id='schedule-calc_groupteacher' class='form-control' name='Schedule[calc_groupteacher]'>";
                $teachergroup .= "<option value=''>-выбрать-</option>";
                foreach($teachergroups as $tg){
                    $teachergroup .= "<option value='".$tg['gid']."'>#".$tg['gid']." ".$tg['gname']."</option>";
                }
                $teachergroup .= "</select>";
                $teachergroup .= "<div class='help-block'></div>";
            
                return $teachergroup;
            }

            //достаем из базы и возвращаем кусок кода для выбора кабинета офиса.
            if (Yii::$app->request->post('OID')) {
                $officecabinets = (new \yii\db\Query())
                ->select('co.id as oid, cco.id as cid, cco.name as cname')
                ->from('calc_cabinetoffice cco')
                ->leftJoin('calc_office co','co.id=cco.calc_office')
                ->where('cco.visible=:vis and co.visible=:vis and co.id=:oid', [':vis'=>1,':oid'=>Yii::$app->request->post('OID')])
                ->orderBy(['cco.name'=>SORT_ASC])
                ->all();

                $officecabinet = "<label class='control-label' for='schedule-calc_cabinetoffice'>".\Yii::t('app','Room')."</label>"; 
                $officecabinet .= "<select id='schedule-calc_cabinetoffice' class='form-control' name='Schedule[calc_cabinetoffice]'>";
                $officecabinet .= "<option value=''>-выбрать-</option>";
                foreach ($officecabinets as $oc) {
                    $officecabinet .= "<option value='".$oc['cid']."'>".$oc['cname']."</option>";
                }
                $officecabinet .= "</select>";
                $officecabinet .= "<div class='help-block'></div>";
                
                return $officecabinet;
            }
    	}
    }
    
    public function actionGetFilters()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
          'status' => true,
          'filtersData' => [
            'days' => [],
            'eduages' => Eduage::getEduages(),
            'eduforms' => Eduform::getEduforms(),
            'languages' => [],
            'offices' => Office::getOfficeByScheduleForBootstrapSelect(),
            'teachers' => [],
          ]
        ];
    }

    public function actionGetInfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
          'status' => true,
          'lessonData' => []
        ];
    }

    public function actionGetHours($teacherId = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
          'status' => true,
          'hoursData' => []
        ];
    }
}
