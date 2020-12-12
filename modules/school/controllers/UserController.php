<?php

namespace app\modules\school\controllers;

use app\modules\school\models\Auth;
use app\modules\school\models\forms\UserTimeTrackingForm;
use app\modules\school\School;
use Yii;
use app\models\City;
use app\models\Office;
use app\models\Teacher;
use app\modules\school\models\Role;
use app\modules\school\models\User;
use app\models\UploadForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        $rules = ['index','create','update','delete','enable','disable','upload','change-password','app-info','view'];
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
     * Выводит табличный список пользователей с информацией по ним.
     * Метод доступен только руководителям (Роль 3) и пользователю 296.
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

        $urlParams = [];

        $urlParams['active'] = $active !== 'all' ? $active : NULL;
        $urlParams['role']   =  $role && $role !== 'all' ? $role : NULL;;

        return $this->render('index', [
            'statuses'  => Role::getRolesList(),
            'urlParams' => $urlParams,
            'users'     => User::getUserListFiltered($urlParams),
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
            'statuses' => Role::getRolesListSimple(),
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

        return $this->render('update', [
            'model'    => $model,
            'teachers' => Teacher::getTeachersInUserListSimple(),
            'statuses' => Role::getRolesListSimple(),
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
        $model = new UserTimeTrackingForm([
            'start' => date('d.m.Y H:i'),
            'end' => date('d.m.Y H:i'),
        ]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->userId = $id;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Запись успешно внесена в журнал рабочего времени!');
                return $this->redirect(['user/view', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить запись в журнал рабочего времени!');
            }
        }

        return $this->render('view', [
            'can' => [
                'createTimeTracking' => $user->roleId === 3 || in_array($user->id, [(int)$id, 296]),
                'updateUser'         => $user->roleId === 3 || in_array($user->id, [296]),
                'updatePassword'     => $user->roleId === 3 || in_array($user->id, [(int)$id, 296]),
            ],
            'model' => $model,
            'user'  => $userModel,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var User|null $model */
        if (($model = User::find()->andWhere(['id' => $id, 'module_type' => School::MODULE_NAME])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
