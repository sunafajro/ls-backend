<?php

namespace app\controllers;

use Yii;
use app\models\ClientAccess;
use app\models\Student;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * ClientaccessController implements the CRUD actions for ClientAccess model.
 */
class ClientaccessController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','update'],
                'rules' => [
                    [
                        'actions' => ['create','update'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Creates a new ClientAccess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($sid)
    {
		// логин для клиента могут создавать только менеджеры или руководители
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            $userInfoBlock = User::getUserInfoBlock();
            // создаем пустую модель
            $model = new ClientAccess();
        
            // находим информацию по клиенту
            $student = Student::findOne($sid);
            // если прилетел post-запрос
            if ($model->load(Yii::$app->request->post())) {
                $model->id = $sid;
                $model->calc_studname = $sid;
                // добавляем дату создания логина и пароля
                $model->date = date('Y-m-d');
                // разрешаем доступ в ЛК
                $model->site = 1;
                // хешируем пароль
                $model->password = md5($model->password);
                // сохраняем модель
                if($model->save()) {
                    Yii::$app->session->setFlash('success', "Логин и пароль к ЛК успешно созданы!");
                } else {
                    Yii::$app->session->setFlash('error', "Не удалось создать логин и парооль к ЛК!");
                }
                // возвращаемся в профиль студента
                return $this->redirect(['studname/view', 'id' => $model->calc_studname]);
            } else {
                // открываем форму и передаем переменные
                return $this->render('create', [
                    'model' => $model,
                    'student' => $student,
                    'userInfoBlock' => $userInfoBlock
                ]);
            }
	    } else {
            // если нет возвращаемся на предыдущую страницу
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Updates an existing ClientAccess model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($sid)
    {
		// логин для клиента могут создавать только менеджеры или руководители
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            $userInfoBlock = User::getUserInfoBlock();
        
    	    $model = ClientAccess::find()->where(['calc_studname'=>$sid])->one();
		
    	    // находим информацию по клиенту
            $student = Student::findOne($sid);
    	    // если прилетел post-запрос
    	    if ($model->load(Yii::$app->request->post())) {
                // хешируем пароль
                $model->password = md5($model->password);
                // обновляем дату
                $model->date = date('Y-m-d');
                // сохраняем модель
                if($model->save()) {
                    Yii::$app->session->setFlash('success', "Логин и пароль к ЛК успешно изменены!");
                } else {
                    Yii::$app->session->setFlash('error', "Не удалось изменить логин или парооль к ЛК!");
                }
                // возвращаемся в профиль студента
                return $this->redirect(['studname/view', 'id' => $model->calc_studname]);
            } else {
                // открываем форму и передаем переменные
                return $this->render('update', [
                        'model' => $model,
                        'student' => $student,
                        'userInfoBlock' => $userInfoBlock
                ]);
            }
        } else {
            // если нет возвращаемся на предыдущую страницу
            return $this->redirect(Yii::$app->request->referrer);
        }
    }


    /**
     * Finds the ClientAccess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClientAccess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClientAccess::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
