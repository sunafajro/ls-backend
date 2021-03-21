<?php

namespace school\controllers;

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
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /** {@inheritDoc} */
    public function behaviors(): array
    {
        $rules = [
            'index','view','create','update','delete','enable','disable',
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
                    'enable' => ['POST'],
                    'disable' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param string $active
     * @param string|null $role
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex(string $active = '1', string $role = null)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'offices' => Office::find()->select(['name'])->andWhere(['visible' => 1])->indexBy('id')->column(),
            'roles'  => Role::find()->select(['name'])->active()->indexBy('id')->column(),
            'statuses' => User::getStatusLabels(),
        ]);
    }

    /**
     * Метод создает нового пользователя.
     * В случае успешности переходим на страничку со списком пользователей.
     * Метод доступен только руководителям (Роль 3) и пользователю 296.
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate()
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $model = new User();
        $model->scenario = 'create';
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                $model->pass        = md5($model->pass);
                $model->module_type = School::MODULE_NAME;
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Пользователь успешно добавлен!');
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось добавить пользователя!');
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model'    => $model,
            'teachers' => Teacher::getTeachersInUserListSimple(),
            'statuses' => Role::find()->select(['name'])->active()->indexBy('id')->column(),
            'offices'  => Office::getOfficesListSimple(),
            'cities'   => City::getCitiesInUserListSimple(),
        ]);
    }

    /**
     * Метод обновляет информацию о пользователе (кроме пароля и фото).
     * В случае успешности переходим на страницу со списком пользователей.
     * Метод доступен только руководителям (Роль 3).
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

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

        $roles = Role::find()->active()->all();
        $roles = ArrayHelper::map($roles, 'id', 'name');

        return $this->render('update', [
            'model'    => $model,
            'teachers' => Teacher::getTeachersInUserListSimple(),
            'statuses' => $roles,
            'offices'  => Office::getOfficesListSimple(),
            'cities'   => City::getCitiesInUserListSimple(),
        ]);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionEnable(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296){
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $model = $this->findModel($id);

        if ($model->visible === 0) {
            $model->visible = 1;
            $model->save(true, ['visible']);
	    }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDisable(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296){
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $model = $this->findModel($id);
        if ($model->visible === 1) {
	        $model->visible = 0;
            $model->save(true, ['visible']);
	    }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionUploadImage(string $id)
    {
        $user = $this->findModel(intval($id));

        /** @var Auth $user */
        $auth = Yii::$app->user->identity;

        if ($auth->roleId !== 3 && in_array($auth->id, [$user->id, 296])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
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
     */
    public function actionDeleteImage(string $id)
    {
        $user = $this->findModel(intval($id));

        /** @var Auth $user */
        $auth = Yii::$app->user->identity;

        if ($auth->roleId !== 3 && in_array($auth->id, [$user->id, 296])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
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
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionChangePassword($id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && !in_array($user->id, [(int)$id, 296])){
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $model = $this->findModel($id);
        $result = [
            'success' => true,
            'message' => null,
        ];
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate(['pass'])) {
                $model->pass = md5($model->pass);
                if (!$model->save(true, ['pass'])) {
                    $result['success'] = false;
                    $result['message'] = $model->getFirstError('pass');
                } else {
                    Yii::$app->session->setFlash('success', 'Пароль пользователя успешно изменен!');
                }
            } else {
                $result['success'] = false;
                $result['message'] = $model->getFirstError('pass');
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
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && !in_array($user->id, [(int)$id, 296])){
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $userModel = $this->findModel($id);
        $imageForm = new UploadForm();

        return $this->render('view', [
            'can' => [
                'updateUser'       => $user->roleId === 3 || in_array($user->id, [296]),
                'updatePassword'   => $user->roleId === 3 || in_array($user->id, [(int)$id, 296]),
                'viewTimeTracking' => $user->roleId === 3 || $user->id === 296 || ($user->roleId === 4 && in_array($user->id, [(int)$id])),
            ],
            'imageForm' => $imageForm,
            'user'  => $userModel,
        ]);
    }

    /**
     * @param string $id
     * @param string|null $time_tracking_id
     * @param string|null $action
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionTimeTracking(string $id, string $time_tracking_id = null, string $action = null)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && !in_array($user->id, [(int)$id, 296])){
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $userModel = $this->findModel($id);

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
            'can' => [
                'createTimeTracking' => $user->roleId === 3 || in_array($user->id, [(int)$id, 296]),
                'updateTimeTracking' => $user->roleId === 3 || in_array($user->id, [(int)$id, 296]),
                'deleteTimeTracking' => $user->roleId === 3 || in_array($user->id, [(int)$id, 296]),
            ],
            'dataProvider'     => $dataProvider,
            'timeTrackingForm' => $timeTrackingForm,
            'searchModel'      => $searchModel,
            'user'             => $userModel,
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
