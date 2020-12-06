<?php

namespace app\modules\school\controllers;

use app\modules\school\models\Auth;
use app\modules\school\School;
use Yii;
use app\models\City;
use app\models\Office;
use app\models\Teacher;
use app\modules\school\models\Role;
use app\modules\school\models\User;
use app\models\UploadForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
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
            ]
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
            throw new ForbiddenHttpException();
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
            throw new ForbiddenHttpException();
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
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296) {
            throw new ForbiddenHttpException();
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
            throw new ForbiddenHttpException();
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEnable(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296){
            throw new ForbiddenHttpException();
        }

        $model = $this->findModel($id);

        if ($model->visible === 0) {
            $model->visible = 1;
            $model->save(true, ['visible']);
	    }

        return $this->redirect(['index']);
    }

    /**
     * @param string $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDisable(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296){
            throw new ForbiddenHttpException();
        }

        $model = $this->findModel($id);
        if ($model->visible === 1) {
	        $model->visible = 0;
            $model->save(true, ['visible']);
	    }

        return $this->redirect(['index']);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionUpload(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296){
            throw new ForbiddenHttpException();
        }

        $user = $this->findModel($id);
        if ($user !== NULL) {
            $model = new UploadForm();
            if (Yii::$app->request->isPost) {
                $model->file = UploadedFile::getInstance($model, 'file');
                if ($model->file && $model->validate()) {
                    $spath = Yii::getAlias('@uploads/user');
                    $filename = $model->resizeAndSave($spath, $id, 'logo');
                    $user->logo = $filename;
                    $user->save();
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
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionChangePassword($id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== 296){
            throw new ForbiddenHttpException();
        }

        $model = $this->findModel($id);
        $model->pass = '';
        if (Yii::$app->request->post()) {
            $pass = Yii::$app->request->post('User')['pass'];
            $passRepeat = Yii::$app->request->post('User')['pass_repeat'];

            if ($pass === $passRepeat){
                $password = md5($pass);
                $db = (new \yii\db\Query())
                ->createCommand()
                ->update(User::tableName(), ['pass' => $password], ['id'=>$id])
                ->execute();
                Yii::$app->session->setFlash('success', \Yii::t('app','Password successfully changed!'));
                return $this->redirect(['change-password','id' => $id]);
            } else {
                Yii::$app->session->setFlash('error', \Yii::t('app','Passwords did not match!'));
                return $this->redirect(['change-password','id' => $id]);
            }
        }
        return $this->render('change-password', [
            'model' => $model,
        ]);
    }

    /**
     * Запрос данных текущего пользователя (для js приложений)
     * @return mixed
     */
    public function actionAppInfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'userData' => User::getUserInfo()
        ];
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function actionView(string $id)
    {
        /** @var Auth $user */
        $user = Yii::$app->user->identity;

        if ($user->roleId !== 3 && $user->id !== (int)$id){
            throw new ForbiddenHttpException();
        }

        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
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
