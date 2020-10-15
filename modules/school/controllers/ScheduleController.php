<?php
namespace app\modules\school\controllers;
use Yii;
use app\models\Eduage;
use app\models\Eduform;
use app\models\Language;
use app\models\Office;
use app\models\Room;
use app\models\Schedule;
use app\models\Teacher;
use app\models\Tool;
use app\modules\school\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
/**
 * ScheduleController implements the CRUD actions for Schedule model.
 */
class ScheduleController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors() : array
    {
        $rules = [
            'app-actions', 'app-create', 'app-update',
            'app-delete', 'app-filters', 'app-groups',
            'app-hours', 'app-lessons', 'app-offices',
            'app-rooms', 'app-teachers', 'index'
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
     * @inheritDoc
     */
    public function beforeAction($action) : bool
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
     * Главная страница раздела Расписание
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Действия доступные пользователю
     *
     * @return mixed
     */
    public function actionAppActions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            "actions" => [
                "create" => User::checkAccess('schedule', 'create'),
                "delete" => User::checkAccess('schedule', 'delete'),
                "hours"  => User::checkAccess('schedule', 'hours'),
                "update" => User::checkAccess('schedule', 'update'),
                "view"   => User::checkAccess('schedule', 'index')
            ]
        ];
    }

    /**
     * Создание записи в расписании
     *
     * @return mixed
     */
    public function actionAppCreate()
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

    /**
     * Обновление записи в расписании
     * @param int $id
     *
     * @return mixed
     */
    public function actionAppUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var Schedule $model */
        $model = $this->findModel($id);
        if ((int)Yii::$app->session->get('user.ustatus') === 5 && (int)$model->calc_teacher !== (int)Yii::$app->session->get('user.uteacher')) {
            Yii::$app->response->statusCode = 403;
            return [
                "message" => "Вам не разрешено производить это действие!"
            ];
        }
        ;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                "message" => "Занятие успешно обновлено!"
            ];
        } else {
            return [
                "message" => "Не удалось обновить занятие!"
            ];
        }
    }

    /**
     * Удаление записи из расписания
     * @param int $id
     *
     * @return mixed
     */
    public function actionAppDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        if ((int)Yii::$app->session->get('user.ustatus') === 5 && (int)$model->calc_teacher !== (int)Yii::$app->session->get('user.uteacher')) {
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

    /**
     * Фильтры доступные пользователю
     *
     * @return mixed
     */
    public function actionAppFilters()
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
                'eduages'   => $tool->prepareDataForSelectElement(Eduage::getEduAges()),
                'eduforms'  => $tool->prepareDataForSelectElement($eduforms),
                'languages' => $tool->prepareDataForSelectElement($languages),
                'offices'   => $tool->prepareDataForSelectElement($offices),
                'teachers'  => $tool->prepareDataForSelectElement($teachers),
            ]
        ];
    }

    /**
     * @param int|null $oid
     * @param int|null $tid
     *
     * @return mixed
     */
    public function actionAppHours($oid = NULL, $tid = NULL)
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

    /**
     * @param int|null $aid
     * @param int|null $did
     * @param int|null $fid
     * @param int|null $lid
     * @param int|null $oid
     * @param int|null $tid
     * @return mixed
     */
    public function actionAppLessons($aid = NULL, $did = NULL, $fid = NULL, $lid = NULL, $oid = NULL, $tid = NULL)
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

    /**
     * @param int $tid
     *
     * @return mixed
     */
    public function actionAppGroups($tid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tool = new Tool();
        $teacher = new Teacher();
        $groups = $teacher->getActiveTeacherGroups($tid);
        return [
            'groups' => $tool->prepareDataForSelectElement($groups)
        ];
    }

    /**
     * @return mixed
     */
    public function actionAppOffices()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'offices' => (new Tool())->prepareDataForSelectElement(Office::getOfficesList())
        ];
    }

    /**
     * @param int $oid
     *
     * @return mixed
     */
    public function actionAppRooms($oid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tool = new Tool();
        $room = new Room();
        $rooms = $room->getRooms($oid);
        return [
            'rooms' => $tool->prepareDataForSelectElement($rooms)
        ];
    }

    /**
     * @param int|null $tid
     * 
     * @return mixed
     */
    public function actionAppTeachers($tid = NULL)
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
     * Finds the Schedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Schedule the loaded model
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