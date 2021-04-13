<?php

namespace school\controllers;
use school\models\AccessRule;
use school\models\Auth;
use Yii;
use school\models\Eduage;
use school\models\Eduform;
use school\models\Language;
use school\models\Office;
use school\models\Room;
use school\models\Schedule;
use school\models\Teacher;
use school\models\Tool;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;

/**
 * Class ScheduleController
 * @package school\controllers
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
     * @param $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $controllerId = $action->controller->id;
            $actionId = str_replace('app-', '', $action->id);
            if (AccessRule::checkAccess("{$controllerId}_{$actionId}") == false) {
                throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
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
        return $this->asJson([
            "actions" => [
                "create" => AccessRule::checkAccess('schedule_create'),
                "delete" => AccessRule::checkAccess('schedule_delete'),
                "hours"  => AccessRule::checkAccess('schedule_hours'),
                "update" => AccessRule::checkAccess('schedule_update'),
                "view"   => AccessRule::checkAccess('schedule_index')
            ]
        ]);
    }

    /**
     * Создание записи в расписании
     *
     * @return mixed
     */
    public function actionAppCreate()
    {
        $model = new Schedule();
        $model->load(Yii::$app->request->post());
        if ($model->save()) {
            return $this->asJson([
                'id' => $model->id,
                'message' => 'Занятие успешно добавлено в расписание!'
            ]);
        } else {
            Yii::$app->response->statusCode = 500;
            return $this->asJson([
                'message' => 'Не удалось добавить занятие в расписание!'
            ]);
        }
    }

    /**
     * Обновление записи в расписании
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionAppUpdate($id)
    {
        /** @var Auth $auth */
        $auth = \Yii::$app->user->identity;
        $model = $this->findModel($id);
        if ($auth->roleId === 5 && $model->calc_teacher !== $auth->teacherId) {
            Yii::$app->response->statusCode = 403;
            return $this->asJson([
                "message" => "Вам не разрешено производить это действие!"
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson([
                "message" => "Занятие успешно обновлено!"
            ]);
        } else {
            Yii::$app->response->statusCode = 500;
            return $this->asJson([
                "message" => "Не удалось обновить занятие!"
            ]);
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
        /** @var Auth $auth */
        $auth = \Yii::$app->user->identity;
        $model = $this->findModel($id);
        if ($auth->roleId === 5 && $model->calc_teacher !== $auth->teacherId) {
            Yii::$app->response->statusCode = 403;
            return $this->asJson([
                "message" => "Вам не разрешено производить это действие!"
            ]);
        }
        if ((int)$model->visible === 1) {
            $model->visible = 0;
            if ($model->save(true, ['visible'])) {
                return $this->asJson([
                    "message" => "Занятие успешно удалено из расписания!"
                ]);
            } else {
                Yii::$app->response->statusCode = 500;
                return $this->asJson([
                    "message" => "Ошибка при удалении занятия!"
                ]);
            }
        } else {
            return $this->asJson([
                "message" => "Занятие успешно удалено из расписания!"
            ]);
        }
    }

    /**
     * Фильтры доступные пользователю
     *
     * @return mixed
     */
    public function actionAppFilters()
    {
        $eduform = new Eduform();
        $eduforms = $eduform->getEduforms();
        $office = new Office();
        $offices = $office->getOfficeBySchedule();
        $language = new Language();
        $languages = $language->getTeachersLanguages();
        $teacher = new Teacher();
        $teachers = $teacher->getTeachersInSchedule();
        $tool = new Tool();
        return $this->asJson([
            'filters' => [
                'eduages'   => $tool->prepareDataForSelectElement(Eduage::getEduAges()),
                'eduforms'  => $tool->prepareDataForSelectElement($eduforms),
                'languages' => $tool->prepareDataForSelectElement($languages),
                'offices'   => $tool->prepareDataForSelectElement($offices),
                'teachers'  => $tool->prepareDataForSelectElement($teachers),
            ]
        ]);
    }

    /**
     * @param int|null $oid
     * @param int|null $tid
     *
     * @return mixed
     */
    public function actionAppHours($oid = NULL, $tid = NULL)
    {
        $schedule = new Schedule();
        $params = [
            'oid' => $oid,
            'tid' => $tid
        ];
        return $this->asJson([
            'columns' => $schedule->getTableColumns('hours'),
            'hours' => $schedule->getTeacherHours($params)
        ]);
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
        $schedule = new Schedule();
        $params = [
            'aid' => $aid,
            'did' => $did,
            'fid' => $fid,
            'lid' => $lid,
            'oid' => $oid,
            'tid' => $tid,
        ];
        return $this->asJson([
            'columns' => $schedule->getTableColumns(),
            'lessons' => $schedule->getScheduleData($params)
        ]);
    }

    /**
     * @param int $tid
     *
     * @return mixed
     */
    public function actionAppGroups($tid)
    {
        $tool = new Tool();
        $teacher = new Teacher();
        $groups = $teacher->getActiveTeacherGroups($tid);
        return $this->asJson([
            'groups' => $tool->prepareDataForSelectElement($groups)
        ]);
    }

    /**
     * @return mixed
     */
    public function actionAppOffices()
    {
        return $this->asJson([
            'offices' => (new Tool())->prepareDataForSelectElement(Office::getOfficesList())
        ]);
    }

    /**
     * @param int $oid
     *
     * @return mixed
     */
    public function actionAppRooms($oid)
    {
        $tool = new Tool();
        $room = new Room();
        $rooms = $room->getRooms($oid);
        return $this->asJson([
            'rooms' => $tool->prepareDataForSelectElement($rooms)
        ]);
    }

    /**
     * @param int|null $tid
     * 
     * @return mixed
     */
    public function actionAppTeachers($tid = NULL)
    {
        /** @var Auth $auth */
        $auth = \Yii::$app->user->identity;
        if ($auth->roleId === 5) {
            $tid = $auth->teacherId;
        }
        $tool = new Tool();
        $teacher = new Teacher();
        $teachers = $teacher->getTeachersWithActiveGroups($tid);
        return $this->asJson([
            'teachers' => $tool->prepareDataForSelectElement($teachers)
        ]);
    }

    /**
     * @param integer $id
     * @return Schedule
     * @throws NotFoundHttpException
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