<?php

namespace app\controllers;

use Yii;
use app\models\Eduage;
use app\models\Eduform;
use app\models\Office;
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
    public function actionIndex($t = null)
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($t === 'filters') {
                $eduages = Eduage::getEduages();
                $eduforms = Eduform::getEduforms();
                $offices = Office::getOfficeBySchedule();
                return [
                    'filters' => [
                        'eduages' => Tool::prepareDataForSelectElement($eduages),
                        'eduforms' => Tool::prepareDataForSelectElement($eduforms),
                        'languages' => [],
                        'offices' => Tool::prepareDataForSelectElement($offices),
                        'teachers' => [],
                    ]
                ];
            } else if ($t === 'lessons') {
                return [
                    'columns' => Schedule::getTableColumns(),
                    'lessons' => []
                ];
            } else if ($t === 'hours') {
                return [
                    'columns' => Schedule::getTableColumns('hours'),
                    'hours' => Schedule::getTeacherHours($oid)
                ];
            }
            return null;
        } else {
            return $this->render('index');
        }
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
}
