<?php

namespace app\modules\school\controllers;

use app\models\Teacher;
use app\models\UploadForm;
use school\models\Auth;
use school\models\City;
use school\models\Office;
use school\models\Role;
use school\models\searches\UserSearch;
use school\models\UserTimeTracking;
use school\models\User;
use school\models\forms\UserTimeTrackingForm;
use school\models\searches\UserTimeTrackingSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class UserController
 * @package app\modules\school\controllers
 */
class UserController extends Controller
{
    public function behaviors(): array
    {
        $rules = ['index', 'create', 'update', 'delete', 'enable', 'disable', 'upload', 'change-password', 'app-info', 'view', 'time-tracking'];
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
    public function actionIndex()
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
            'roles' => $this->getItemListByClass(Role::class),
            'searchModel' => $searchModel,
        ]);
    }

    /**
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
            'statuses' => $this->getItemListByClass(Role::class),
            'offices'  => Office::getOfficesListSimple(),
            'cities'   => $this->getItemListByClass(City::class),
        ]);
    }

    /**
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

        $model = $this->findModel(intval($id));
        $model->scenario = 'update';
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Пользователь успешно изменен!');
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось изменить пользователя!');
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model'    => $model,
            'teachers' => Teacher::getTeachersInUserListSimple(),
            'statuses' => $this->getItemListByClass(Role::class),
            'offices'  => Office::getOfficesListSimple(),
            'cities'   => $this->getItemListByClass(City::class),
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
    public function actionUpload(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296){
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $user = $this->findModel($id);
        if ($user !== NULL) {
            $model = new UploadForm();
            if (Yii::$app->request->isPost) {
                $model->file = UploadedFile::getInstance($model, 'file');
                if ($model->file && $model->validate()) {
                    $filename = $model->resizeAndSave(Yii::getAlias('@uploads/user'), $id, 'logo');
                    $user->logo = $filename;
                    if ($user->save()) {
                        Yii::$app->session->setFlash('success', 'Изображение пользователя успешно изменено!');
                    } else {
                        Yii::$app->session->setFlash('error', 'Не удалось изменить изображение пользователя!');
                    }
                    return $this->redirect(['user/upload','id' => $id]);
                }
            }
            return $this->render('upload', [
                'model' => $model,
                'user'  => $user,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'The requested page does not exist.'));
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

        return $this->render('view', [
            'can' => [
                'updateUser'       => $user->roleId === 3 || in_array($user->id, [296]),
                'updatePassword'   => $user->roleId === 3 || in_array($user->id, [(int)$id, 296]),
                'viewTimeTracking' => $user->roleId === 3 || $user->id === 296 || ($user->roleId === 4 && in_array($user->id, [(int)$id])),
            ],
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
        $dataProvider = $searchModel->search(UserTimeTracking::find()->byEntityId((int)$id)->byActive(), Yii::$app->request->get());

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
        if (($model = User::find()->byId($id)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param string $class
     * @return array
     */
    private function getItemListByClass(string $class): array
    {
        $cities = [];
        foreach($class::find()->select('name')->byActive()->orderBy('name')->all() as $c) {
            $cities[strval($c->id)] = $c->name;
        }

        return $cities;
    }
}
