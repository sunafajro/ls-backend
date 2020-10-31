<?php

namespace app\modules\school\controllers;

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
        $rules = ['index','create','update','delete','enable','disable','upload','changepass', 'app-info'];
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
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.uid') !== 296) {
            throw new ForbiddenHttpException();
        }
        $urlParams = [];

        $urlParams['active'] = $active !== 'all' ? $active : NULL;
        $urlParams['role']   =  $role && $role !== 'all' ? $role : NULL;;

        return $this->render('index', [
            'statuses'      => Role::getRolesList(),
            'urlParams'     => $urlParams,
            'userInfoBlock' => User::getUserInfoBlock(),
            'users'         => User::getUserListFiltered($urlParams),
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
        if(Yii::$app->session->get('user.ustatus') != 3 && (int)Yii::$app->session->get('user.uid') !== 296){
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
            'model'         => $model,
            'teachers'      => Teacher::getTeachersInUserListSimple(),
            'statuses'      => Role::getRolesListSimple(),
            'offices'       => Office::getOfficesListSimple(),
            'cities'        => City::getCitiesInUserListSimple(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * Метод обновляет информацию о пользователе (кроме пароля и фото).
     * В случае успешности переходим на страницу со списком пользователей.
     * Метод доступен только руководителям (Роль 3).
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if ((int)Yii::$app->session->get('user.ustatus')!==3 && (int)Yii::$app->session->get('user.uid') !== 296) {
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
            'model'         => $model,
            'teachers'      => Teacher::getTeachersInUserListSimple(),
            'statuses'      => Role::getRolesListSimple(),
            'offices'       => Office::getOfficesListSimple(),
            'cities'        => City::getCitiesInUserListSimple(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     *
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        if (Yii::$app->session->get('user.ustatus')!=3){
            throw new ForbiddenHttpException();
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionEnable($id)
    {
        if((int)Yii::$app->session->get('user.ustatus')!==3 && (int)Yii::$app->session->get('user.uid') !== 296){
            return $this->redirect(Yii::$app->request->referrer);
        }

        // получаем информацию по пользователю
        $model=$this->findModel($id);
        //проверяем текущее состояние
        if($model->visible==0){
            $model->visible = 1;
            $model->save();
	}

        return $this->redirect(['index']);
    }

    public function actionDisable($id)
    {
        if((int)Yii::$app->session->get('user.ustatus')!==3 && (int)Yii::$app->session->get('user.uid') !== 296){
            return $this->redirect(Yii::$app->request->referrer);
        }

        // получаем информацию по пользователю
        $model=$this->findModel($id);
        //проверяем текущее состояние
        if($model->visible==1){
	    $model->visible = 0;
            $model->save();
	}
    return $this->redirect(['index']);
    }

    public function actionUpload(string $id)
    {
        if((int)Yii::$app->session->get('user.ustatus') !== 3 &&
           (int)Yii::$app->session->get('user.uid') !== 296) {
            return $this->redirect(Yii::$app->request->referrer);
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
                'user' => $user,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'The requested page does not exist.'));
        }
    }

    public function actionChangepass($id)
    {
        if((int)Yii::$app->session->get('user.ustatus')!==3 && (int)Yii::$app->session->get('user.uid') !== 296){
            return $this->redirect(Yii::$app->request->referrer);
        }

        $model = $this->findModel($id);
        $model->pass = '';
        if(Yii::$app->request->post()){
            $pass = Yii::$app->request->post('User')['pass'];
            $passr = Yii::$app->request->post('User')['pass_repeat'];

            if($pass == $passr){
                $password = md5($pass);
                $db = (new \yii\db\Query())
                ->createCommand()
                ->update(User::tableName(), ['pass' => $password], ['id'=>$id])
                ->execute();
                Yii::$app->session->setFlash('success', \Yii::t('app','Password succesfuly changed!'));
                return $this->redirect(['changepass','id'=>$id]);
            } else {
                Yii::$app->session->setFlash('error', \Yii::t('app','Passwords did not match!'));
                return $this->redirect(['changepass','id'=>$id]);
            }
        }
        return $this->render('changepass', [
            'model' => $model,
            'userInfoBlock' => User::getUserInfoBlock()
        ]);
    }

    /**
     * Запрос данных текущего пользователя (для js приложений)
     * @return array
     */
    public function actionAppInfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'userData' => User::getUserInfo()
        ];
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
