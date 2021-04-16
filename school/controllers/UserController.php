<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use school\models\AccessRule;
use school\models\Auth;
use school\models\forms\UserTimeTrackingForm;
use school\models\searches\UserSearch;
use school\models\searches\UserTimeTrackingSearch;
use school\models\UserImage;
use school\models\UserTimeTracking;
use school\School;
use Yii;
use school\models\City;
use school\models\Office;
use school\models\Teacher;
use school\models\Role;
use school\models\User;
use school\models\forms\UploadForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class UserController
 * @package school\controllers
 */
class UserController extends BaseController
{
    /** @var {inheritDoc} */
    protected $actionCustomAccessRules = [
        'view'            => 'rule.user.view.any',
        'change-password' => 'rule.user.change-password.any',
        'upload-image'    => 'rule.user.upload-image.any',
        'delete-image'    => 'rule.user.delete-image.any',
    ];

    /** {@inheritDoc} */
    public function behaviors(): array
    {
        $rules = [
            'index','view','create','update','delete','restore','remove',
            'download-image','upload-image','delete-image',
            'change-password','time-tracking','app-info',
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
                    'change-password' => ['POST'],
                    'restore' => ['POST'],
                    'remove' => ['POST'],
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

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'roles' => $this->getEntityItems(Role::class),
            'offices' => $this->getEntityItems(Office::class),
            'statuses' => User::getStatusLabels(),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = 'main-2-column';

        $model = new User();
        $model->scenario = 'create';
        if (Yii::$app->request->isPost) {
            $t = Yii::$app->db->beginTransaction();
            try {
                if ($model->load(Yii::$app->request->post())) {
                    $isNewTeacher = $model->calc_teacher === '0';
                    if ($isNewTeacher) {
                        $teacher = new Teacher();
                        $teacher->scenario = Teacher::SCENARIO_CREATE_FROM_USER;
                        $teacher->name = $model->name;
                        if (!$teacher->save()) {
                            throw new \Exception();
                        }
                        $model->calc_teacher = $teacher->id;
                    }
                    $model->pass        = md5($model->pass);
                    $model->module_type = School::MODULE_NAME;

                    if ($model->save()) {
                        $t->commit();
                        if ($isNewTeacher) {
                            Yii::$app->session->setFlash('success', 'Преподаватель успешно добавлен, но необходимо заполнить дополнительную информацию!');
                            return $this->redirect(['teacher/update', 'id' => $teacher->id]);
                        } else {
                            Yii::$app->session->setFlash('success', 'Пользователь успешно добавлен!');
                            return $this->redirect(['user/index']);
                        }
                    } else {
                        throw new \Exception();
                    }
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Не удалось добавить пользователя!');
                $model->pass = '';
                $t->rollBack();
            }
        }

        return $this->render('create', [
            'model' => $model,
            'teachers' => Teacher::getTeachersInUserListSimple(),
            'roles' => $this->getEntityItems(Role::class),
            'offices' => $this->getEntityItems(Office::class),
            'cities' => $this->getEntityItems(City::class),
        ]);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $id)
    {
        $this->layout = 'main-2-column';

        $model = $this->findModel($id);
        $model->scenario = 'update';
        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post($model->formName());
            if ($model->load(Yii::$app->request->post())) {
                if (!isset($postData['calc_teacher'])) {
                    $model->calc_teacher = 0;
                }
                if (!isset($postData['calc_office'])) {
                    $model->calc_office = 0;
                }
                if (!isset($postData['calc_city'])) {
                    $model->calc_city = 0;
                }
                if (!isset($postData['$model->logo'])) {
                    $model->logo = '';
                }
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Пользователь успешно изменен!');
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось изменить пользователя!');
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'teachers' => Teacher::getTeachersInUserListSimple(),
            'roles' => $this->getEntityItems(Role::class),
            'offices' => $this->getEntityItems(Office::class),
            'cities' => $this->getEntityItems(City::class),
        ]);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(string $id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionRestore(string $id)
    {
        $this->findModel($id)->restore();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionRemove(string $id)
    {
        $this->findModel($id)->softDelete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionUploadImage(string $id)
    {
        $user = $this->findModel(intval($id));

        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!AccessRule::checkAccess('rule.user.upload-image.any') && $auth->id !== $user->id) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $oldImage = $user->image;

        $model = new UploadForm();
        $model->file = UploadedFile::getInstance($model, 'file');
        if ($model->file && $model->validate()) {
            if ($model->saveFile(UserImage::getTempDirPath(), true)) {
                $file = new UserImage([
                    'file_name'     => $model->file_name,
                    'original_name' => $model->original_name,
                    'size'          => $model->file->size,
                ]);
                if ($file->save()) {
                    $file->setEntity(UserImage::TYPE_USER_IMAGE, $user->id);
                    if (!empty($oldImage)) {
                        $oldImage->delete();
                    }
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Image successfully uploaded.'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to upload image.'));
            }
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to upload image.'));
        }
        return $this->redirect(['user/view', 'id' => $user->id]);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDownloadImage(string $id)
    {
        $user = $this->findModel(intval($id));
        if (($file = $user->image) !== null) {
            return Yii::$app->response->sendFile($file->getPath(), $file->original_name, ['inline' => true]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'File not found.'));
        }
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDeleteImage(string $id)
    {
        $user = $this->findModel(intval($id));

        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!AccessRule::checkAccess('rule.user.delete-image.any') && $auth->id !== $user->id) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        if (($file = $user->image) !== null) {
            if ($file->delete()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Image successfully deleted.'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete image.'));
            }
            return $this->redirect(['user/view', 'id' => $user->id]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'File not found.'));
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     * @throws ForbiddenHttpException
     */
    public function actionChangePassword($id)
    {
        $user = $this->findModel($id);

        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!AccessRule::checkAccess('rule.user.change-password.any') && $auth->id !== $user->id) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $result = [
            'success' => true,
            'message' => null,
        ];
        if ($user->load(Yii::$app->request->post())) {
            if ($user->validate(['pass'])) {
                $user->pass = md5($user->pass);
                if (!$user->save(true, ['pass'])) {
                    $result['success'] = false;
                    $result['message'] = $user->getFirstError('pass');
                } else {
                    Yii::$app->session->setFlash('success', 'Пароль пользователя успешно изменен!');
                }
            } else {
                $result['success'] = false;
                $result['message'] = $user->getFirstError('pass');
            }
        }

        return $this->asJson($result);
    }

    /**
     * Запрос данных текущего пользователя (для js приложений)
     * @return mixed
     */
    public function actionAppInfo()
    {
        return $this->asJson([
            'userData' => User::getUserInfo()
        ]);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionView(string $id)
    {
        $this->layout = 'main-2-column';
        $user = $this->findModel($id);

        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!AccessRule::checkAccess('rule.user.view.any') && $auth->id !== $user->id) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $imageForm = new UploadForm();

        return $this->render('view', [
            'imageForm' => $imageForm,
            'user'  => $user,
        ]);
    }

    /**
     * @param string $id
     * @param string|null $time_tracking_id
     * @param string|null $action
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionTimeTracking(string $id, string $time_tracking_id = null, string $action = null)
    {
        $this->layout = 'main-2-column';
        $user = $this->findModel($id);

        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!AccessRule::checkAccess('rule.time-tracking.manage.any') && $auth->id !== $user->id) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
        }

        $timeTracking = $time_tracking_id
            ? UserTimeTracking::find()->byId((int)$time_tracking_id)->byEntityId((int)$id)->one()
            : null;

        $timeTrackingForm = new UserTimeTrackingForm([
            'start' => date('d.m.Y H:i'),
            'end'   => date('d.m.Y H:i'),
        ]);

        if (!empty($timeTracking)) {
            $timeTrackingForm->loadFromModel($timeTracking);
        }

        if (Yii::$app->request->isPost) {
            if ($timeTrackingForm->id && $action === 'delete') {
                if (!empty($timeTracking)) {
                    if ($timeTracking->delete()) {
                        Yii::$app->session->setFlash(
                            'success',
                            "Запись #{$time_tracking_id} успешно удалена из журнала рабочего времени!"
                        );
                    } else {
                        Yii::$app->session->setFlash(
                            'error',
                            "Не удалось удалить запись #{$time_tracking_id} из журнала рабочего времени!"
                        );
                    }
                } else {
                    Yii::$app->session->setFlash(
                        'warning',
                        "Не удалось найти запись #{$time_tracking_id} в журнале рабочего времени!"
                    );
                }
                return $this->redirect(['user/time-tracking', 'id' => $id]);
            } else if ($timeTrackingForm->load(Yii::$app->request->post())) {
                $timeTrackingForm->userId = $id;
                if ($timeTrackingForm->save()) {
                    Yii::$app->session->setFlash(
                        'success',
                        !$timeTrackingForm->id
                            ? "Запись #{$timeTrackingForm->id} успешно внесена в журнал рабочего времени!"
                            : "Запись #{$timeTrackingForm->id} успешно обновлена в журнале рабочего времени!"
                    );

                    return $this->redirect(['user/time-tracking', 'id' => $id]);
                } else {
                    Yii::$app->session->setFlash(
                        'error',
                        !$timeTrackingForm->id
                            ? "Не удалось добавить запись в журнал рабочего времени!"
                            : "Не удалось обновить запись #{$timeTrackingForm->id} в журнале рабочего времени!"
                    );
                }
            }
        }

        $searchModel = new UserTimeTrackingSearch();
        $dataProvider = $searchModel->search(UserTimeTracking::find()->byEntityId((int)$id)->active(), Yii::$app->request->get());

        return $this->render('time-tracking', [
            'dataProvider'     => $dataProvider,
            'timeTrackingForm' => $timeTrackingForm,
            'searchModel'      => $searchModel,
            'user'             => $user,
        ]);
    }

    /**
     * @param integer $id
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): ?User
    {
        /** @var User|null $model */
        if (($model = User::find()->andWhere(['id' => $id, 'module_type' => School::MODULE_NAME])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
