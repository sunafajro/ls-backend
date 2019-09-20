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
                'only' => [
                    'api-actions',
                    'api-create',
                    'api-delete',
                    'api-filters',
                    'api-groups',
                    'api-hours',
                    'api-lessons',
                    'api-offices',
                    'api-rooms',
                    'api-teachers',
                    'api-update',
                    'index'
                ],
                'rules' => [
                    [
                        'actions' => [
                            'api-actions',
                            'api-create',
                            'api-delete',
                            'api-filters',
                            'api-groups',
                            'api-hours',
                            'api-lessons',
                            'api-offices',
                            'api-rooms',
                            'api-teachers',
                            'api-update',
                            'index'
                        ],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'api-actions',
                            'api-create',
                            'api-delete',
                            'api-filters',
                            'api-groups',
                            'api-hours',
                            'api-lessons',
                            'api-offices',
                            'api-rooms',
                            'api-teachers',
                            'api-update',
                            'index'
                        ],
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
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionApiActions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            "actions" => [
                "create" => User::checkAccess('schedule', 'create'),
                "delete" => User::checkAccess('schedule', 'delete'),
                "hours" => User::checkAccess('schedule', 'hours'),
                "update" => User::checkAccess('schedule', 'update'),
                "view" => User::checkAccess('schedule', 'index')
            ]
        ];
    }

    /**
     * Метод позволяет преподавателям, менеджерам и руководителям 
     * создавать записи занятий в расписании.
     */
    public function actionApiCreate($t = null, $tid = null, $oid = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Schedule();
        $model->load(Yii::$app->request->post());
        $model->visible = 1;
        $model->user = Yii::$app->session->get('user.uid');
        $model->data = date('Y-m-d');
        if($model->save()) {
            return [
                'id' => $model->id,
                'message' => 'Занятие успешно добавлено в расписание!'
            ];
        } else {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => 'Не удалось добавить занятие в расписание!'
            ];
        }
    }

    public function actionApiDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        if((int)Yii::$app->session->get('user.ustatus') === 5 && (int)$model->calc_teacher !== (int)Yii::$app->session->get('user.uteacher')) {
            Yii::$app->response->statusCode = 403;
            return [
                "message" => "Вам не разрешено производить это действие!"
            ];
        }
        if ((int)$model->visible === 1) {
            $model->visible = 0;
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

    public function actionApiFilters()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $eduform = new Eduform();
        $eduforms = $eduform->getEduforms();
        $office = new Office();
        $offices = $office->getOfficeBySchedule();
        $language = new Language();
        $languages = $language->getTeachersLanguages();
        $teacher = new Teacher();
        $teachers = $teacher->getTeachersInSchedule();
        $tool = new Tool();
        return [
            'filters' => [
                'eduages' => $tool->prepareDataForSelectElement(Eduage::getEduAges()),
                'eduforms' => $tool->prepareDataForSelectElement($eduforms),
                'languages' => $tool->prepareDataForSelectElement($languages),
                'offices' => $tool->prepareDataForSelectElement($offices),
                'teachers' => $tool->prepareDataForSelectElement($teachers),
            ]
        ];
    }

    public function actionApiHours($oid = NULL, $tid = NULL)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $schedule = new Schedule();
        $params = [
            'oid' => $oid,
            'tid' => $tid
        ];
        return [
            'columns' => $schedule->getTableColumns('hours'),
            'hours' => $schedule->getTeacherHours($params)
        ];
    }

    public function actionApiLessons($aid = NULL, $did = NULL, $fid = NULL, $lid = NULL, $oid = NULL, $tid = NULL)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $schedule = new Schedule();
        $params = [
            'aid' => $aid,
            'did' => $did,
            'fid' => $fid,
            'lid' => $lid,
            'oid' => $oid,
            'tid' => $tid,
        ];
        return [
            'columns' => $schedule->getTableColumns(),
            'lessons' => $schedule->getScheduleData($params)
        ];
    }

    public function actionApiGroups($tid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tool = new Tool();
        $teacher = new Teacher();
        $groups = $teacher->getActiveTeacherGroups($tid);
        return [
            'groups' => $tool->prepareDataForSelectElement($groups)
        ];
    }    

    public function actionApiOffices()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tool = new Tool();
        $office = new Office();
        $offices = $office->getOfficesList();
        return [
            'offices' => $tool->prepareDataForSelectElement($offices)
        ];
    }

    public function actionApiRooms($oid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tool = new Tool();
        $room = new Room();
        $rooms = $room->getRooms($oid);
        return [
            'rooms' => $tool->prepareDataForSelectElement($rooms)
        ];
    }

    public function actionApiTeachers($tid = NULL)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ((int)Yii::$app->session->get('user.ustatus') === 5) {
            $tid = Yii::$app->session->get('user.uteacher');
        }
        $tool = new Tool();
        $teacher = new Teacher();
        $teachers = $teacher->getTeachersWithActiveGroups($tid);
        return [
            'teachers' => $tool->prepareDataForSelectElement($teachers)
        ];
    }

    /**
     * Метод позволяет менеджерам, преподавателям и руководителям
     * изменять записии занятий в расписании.
     * Требуется ID записи.
     */
    public function actionApiUpdate($id)
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