<?php

namespace app\controllers;

use Yii;
use app\models\Eduage;
use app\models\Eduform;
use app\models\Language;
use app\models\Office;
use app\models\Room;
use app\models\Schedule;
use app\models\Teacher;
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
     * Lists all CalcSchedule models.
     * @return mixed
     */
    public function actionIndex($t = null)
    {
        if (Yii::$app->request->isPost) {
            $aid = Yii::$app->request->post('aid') ? Yii::$app->request->post('aid') : null;
            $did = Yii::$app->request->post('did') ? Yii::$app->request->post('did') : null;
            $fid = Yii::$app->request->post('fid') ? Yii::$app->request->post('fid') : null;
            $lid = Yii::$app->request->post('lid') ? Yii::$app->request->post('lid') : null;
            $oid = Yii::$app->request->post('oid') ? Yii::$app->request->post('oid') : null;
            $tid = Yii::$app->request->post('tid') ? Yii::$app->request->post('tid') : null;
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($t === 'actions') {
                return [
                    "actions" => [
                        "create" => User::checkAccess('schedule', 'create'),
                        "delete" => User::checkAccess('schedule', 'delete'),
                        "hours" => User::checkAccess('schedule', 'hours'),
                        "update" => User::checkAccess('schedule', 'update'),
                        "view" => User::checkAccess('schedule', 'index')
                    ]
                ];
            } else if ($t === 'filters') {
                $eduages = Eduage::getEduages();
                $eduforms = Eduform::getEduforms();
                $offices = Office::getOfficeBySchedule();
                $languages = Language::getTeachersLanguages();
                $teachers = Teacher::getTeachersInSchedule();
                return [
                    'filters' => [
                        'eduages' => Tool::prepareDataForSelectElement($eduages),
                        'eduforms' => Tool::prepareDataForSelectElement($eduforms),
                        'languages' => Tool::prepareDataForSelectElement($languages),
                        'offices' => Tool::prepareDataForSelectElement($offices),
                        'teachers' => Tool::prepareDataForSelectElement($teachers),
                    ]
                ];
            } else if ($t === 'lessons') {
                return [
                    'columns' => Schedule::getTableColumns(),
                    'lessons' => Schedule::getScheduleData($aid, $did, $fid, $lid, $oid, $tid)
                ];
            } else if ($t === 'hours') {
                return [
                    'columns' => Schedule::getTableColumns('hours'),
                    'hours' => Schedule::getTeacherHours($oid)
                ];
            }
            Yii::$app->response->statusCode = 400;
            return null;
        } else {
            return $this->render('index');
        }
    }

    /**
     * Метод позволяет преподавателям, менеджерам и руководителям 
     * создавать записи занятий в расписании.
     */
    public function actionCreate($t = null, $tid = null, $oid = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Schedule();
        if ($model->load(Yii::$app->request->post())) {
            // помечаем занятие как действующее
            $model->visible = 1;
            // указываем пользователя добавившего занятие в расписание
            $model->user = Yii::$app->session->get('user.uid');
            // указывае дату добавления занятия в расписание
            $model->data = date('Y-m-d');
            // сохраняем запись
            if($model->save()) {
                return [
                    "message" => "Занятие успешно добавлено в расписание!"
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return [
                    "message" => "Не удалось добавить занятие в расписание!"
                ];
            }
            return $this->redirect(['index']);
        } else {
            if ($t === 'teachers') {
                if ((int)Yii::$app->session->get('user.ustatus') === 5) {
                    $tid = Yii::$app->session->get('user.uteacher');
                }
                $teachers = Teacher::getTeachersWithActiveGroups($tid);
                return [
                    'teachers' => Tool::prepareDataForSelectElement($teachers)
                ];
            } else if ($t === 'offices') {
                $offices = Office::getOfficesList();
                return [
                    'offices' => Tool::prepareDataForSelectElement($offices)
                ];
            } else if ($t === 'groups' && $tid) {
                $groups = Teacher::getActiveTeacherGroups($tid);
                return [
                    'groups' => Tool::prepareDataForSelectElement($groups)
                ];
            } else if ($t === 'rooms' && $oid) {
                $rooms = Room::getRooms($oid);
                return [
                    'rooms' => Tool::prepareDataForSelectElement($rooms)
                ];
            } else {
                Yii::$app->response->statusCode = 400;
                return null;
            }
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

    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // находим занятие
        $model = $this->findModel($id);
        // пользователь преподаватель не может удалять чужие занятия
        if((int)Yii::$app->session->get('user.ustatus') === 5 && (int)$model->calc_teacher !== (int)Yii::$app->session->get('user.uteacher')) {
            Yii::$app->response->statusCode = 403;
            return [
                "message" => "Вам не разрешено производить это действие!"
            ];
        }
        // проверяем текущее состояние
        if ((int)$model->visible === 1) {
            // помечаем занятие как удаленное
            $model->visible = 0;
            // сохраняем занятие
            if ($model->save()) {
                return [
                    "message" => "Занятие успешно удалено из расписания!"
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return [
                    "message" => "Ошибка при удалении занятия!"
                ];
            }
        } else {
            return [
                "message" => "Занятие успешно удалено из расписания!"
            ];
        }
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
