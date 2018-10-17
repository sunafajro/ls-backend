<?php

namespace app\controllers;

use Yii;
use app\models\City;
use app\models\Office;
use app\models\Role;
use app\models\Teacher;
use app\models\Tool;
use app\models\User;
use app\models\UploadForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','create','update','delete','enable','disable','upload','changepass', 'get-info'],
                'rules' => [
                    [
                        'actions' => ['index','create','update','delete','enable','disable','upload','changepass', 'get-info'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index','create','update','enable','disable','upload','changepass', 'get-info'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                     [
                        'actions' => ['delete'],
                        'allow' => false,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Выводит табличный список пользователей с информацией по ним.
     * Метод доступен только руководителям (Роль 3).
     * @return mixed
     */
    public function actionIndex()
    {
      if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.uid') !== 296){
        return $this->redirect(Yii::$app->request->referrer);
      }
      $url_params = [
        'active' => 1,
        'role' => NULL,
      ];

      if(isset($_GET['active'])) {
        $url_params['active'] = $_GET['active'] !== 'all' ? $_GET['active'] : NULL;
      }
      if(isset($_GET['role'])) {
        $url_params['role'] =  $_GET['role'] !== 'all' ? $_GET['role'] : NULL;;
      }

      return $this->render('index', [
        'userInfoBlock' => User::getUserInfoBlock(),
        'users' => User::getUserListFiltered($url_params),
        'statuses' => Role::getRolesList(),
        'url_params' => $url_params,
      ]);
    }

    /**
     * Метод создает новго пользователя.
     * В случае успешности переходим на страничку со списком пользователей.
     * Метод доступен только руководителям (Роль 3).
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->session->get('user.ustatus') != 3 && (int)Yii::$app->session->get('user.uid') !== 296){
            return $this->redirect(Yii::$app->request->referrer);
        }

        $model = new User();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post())) {
			$model->pass = md5($model->pass);
			$model->site = 1;
			$model->visible = 1;
			if($model->calc_teacher == NULL) {
                $model->calc_teacher = 0;
			}
			if($model->calc_office == NULL) {
			    $model->calc_office = 0;
			}
            if($model->calc_city == NULL) {
                $model->calc_city = 0;
            }
            if($model->logo == NULL) {
                $model->logo = '';
            }
			if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Пользователь успешно добавлен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить пользователя!');
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'teachers' => Teacher::getTeachersInUserListSimple(),
                'statuses' => Role::getRolesListSimple(),
                'offices' => Office::getOfficesListSimple(),
                'cities' => City::getCitiesInUserListSimple(),
                'userInfoBlock' => User::getUserInfoBlock(),
            ]);
        }
    }

    /**
     * Метод обновляет информацию о пользователе (кроме пароля и фото).
     * В случае успешности переходим на страницу со списком пользователей.
     * Метод доступен только руководителям (Роль 3).
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if((int)Yii::$app->session->get('user.ustatus')!==3 && (int)Yii::$app->session->get('user.uid') !== 296){
            return $this->redirect(Yii::$app->request->referrer);
        }

        $model = $this->findModel($id);
        $model->scenario = 'update';
        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            if ($model->calc_teacher == NULL) {
                $model->calc_teacher = 0;
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Пользователь успешно изменен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить пользователя!');
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'teachers' => Teacher::getTeachersInUserListSimple(),
                'statuses' => Role::getRolesListSimple(),
                'offices' => Office::getOfficesListSimple(),
                'cities' => City::getCitiesInUserListSimple(),
                'userInfoBlock' => User::getUserInfoBlock(),
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(Yii::$app->session->get('user.ustatus')!=3){
            return $this->redirect(Yii::$app->request->referrer);
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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

    public function actionUpload($id)
    {
        if((int)Yii::$app->session->get('user.ustatus')!==3 && (int)Yii::$app->session->get('user.uid') !== 296){
            return $this->redirect(Yii::$app->request->referrer);
        }
        // подключаем боковую панель
        $this->layout = 'column2';
        // находим пользователя
        $user = $this->findModel($id);

        if(!empty($user)){
        // создаем новую модель загрузки
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()) {
                //задаем адрес папки с файлами для поиска
                $spath = "uploads/user/";
                //задаем адрес папки для загрузки файла
                $filepath = "uploads/user/".$id."/logo/";
                //задаем имя файла
                $filename = "calc-language-school-ru-".$id.".".$model->file->extension;
                //проверяем наличие файла и папки
                $filesearch = FileHelper::findFiles($spath,['only'=>[$filename]]);
                // если нет
                if(empty($filesearch)){
                    // создаем папку по идентификатору пользователя
                    FileHelper::createDirectory($spath.$id."/");
                    // в ней создаем папку logo
                    FileHelper::createDirectory($spath.$id."/logo/");
                }
                $model->file->saveAs($filepath.$filename);
                $logoname = 'calc-language-school-ru-'.$id.'.'.$model->file->extension;
                $db = (new \yii\db\Query())
                ->createCommand()
                ->update('user', ['logo' => $logoname], ['id'=>$id])
                ->execute();
                return $this->redirect(['user/upload','id'=>$id]);
            }
        }

        return $this->render('upload', ['model' => $model,'user'=>$user]);
        }
        else{
            return $this->redirect(['user/index']);
        }
    }

    public function actionChangepass($id)
    {
        if((int)Yii::$app->session->get('user.ustatus')!==3 && (int)Yii::$app->session->get('user.uid') !== 296){
            return $this->redirect(Yii::$app->request->referrer);
        }

        $this->layout = 'column2';
        $model = $this->findModel($id);
        $model->pass = '';
        if(Yii::$app->request->post()){
            $pass = Yii::$app->request->post('User')['pass'];
            $passr = Yii::$app->request->post('User')['pass_repeat'];

            if($pass == $passr){
                $password = md5($pass);
                $db = (new \yii\db\Query())
                ->createCommand()
                ->update('user', ['pass' => $password], ['id'=>$id])
                ->execute();
                Yii::$app->session->setFlash('success', \Yii::t('app','Password succesfuly changed!'));
                return $this->redirect(['changepass','id'=>$id]);
            } else {
                Yii::$app->session->setFlash('error', \Yii::t('app','Passwords did not match!'));
                return $this->redirect(['changepass','id'=>$id]);
            }
        }
        return $this->render('changepass', ['model' => $model]);
    }

    public function actionGetInfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'status' => true,
            'userData' => User::getUserInfo()
        ];
    }
}
