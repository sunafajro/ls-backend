<?php

namespace school\controllers;

use Yii;
use school\models\Studphone;
use school\models\Student;
use school\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * StudphoneController implements the CRUD actions for Studphone model.
 */
class StudphoneController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','update','delete'],
                'rules' => [
                    [
                        'actions' => ['create','update','delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','update','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Creates a new Studphone model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($sid)
    {
        /* проверям роль пользователя */
        if(!Yii::$app->session->get('user.ustatus') == 3 && !Yii::$app->session->get('user.ustatus') == 4) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        
        $userInfoBlock = User::getUserInfoBlock();
        $model = new Studphone();       
        
        // проверяем что такой студент есть в базе
        if (($student = Student::findOne($sid)) !== null) {
            $student = Student::findOne($sid);
        }

        if ($model->load(Yii::$app->request->post())) {
            // подставляем id студента
            $model->calc_studname = $sid;
            // на всякий случай проверям, чистим строку от всего кроме цифр
            $model->phone = preg_replace('/[^0-9]/', '', $model->phone);
            // срезаем пробелы с обеих сторон
            $model->description = trim($model->description);
            // указываем что номер активен
            $model->visible = 1;
            // указываем дату добавления
            $model->create_date = date('Y-m-d');
            // указываем id пользователя добавившего телефон
            $model->create_user = Yii::$app->session->get('user.uid');
            // сохраняем модель
            if($model->save()) {
                // если успешно, задаем сообщение об успешности
                Yii::$app->session->setFlash('success', 'Телефон клиента успешно добавлен!');
            } else {
                // если не успешно задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Не удалось добавить телефон клиента!');
            }
            return $this->redirect(['studphone/create', 'sid' => $model->calc_studname]);
        } else {
            // выбираем телефоны клиента
            $phones = (new \yii\db\Query())
            ->select('id as id, calc_studname as sid, phone as phone, description as description')
            ->from('calc_studphone')
            ->where('visible=:vis', [':vis'=>1])
            ->andWhere(['calc_studname'=>$sid])
            ->all();

            return $this->render('create', [
                'model' => $model,
                'student' => $student,
                'phones' => $phones,
                'userInfoBlock' => $userInfoBlock
            ]);
        }
    }

    /**
     * Updates an existing Studphone model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /* проверям роль пользователя */
        if(!Yii::$app->session->get('user.ustatus') == 3 && !Yii::$app->session->get('user.ustatus') == 4) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        
        $userInfoBlock = User::getUserInfoBlock();
        $model = $this->findModel($id);
        
        // проверяем что такой студент есть в базе
        if (($student = Student::findOne($model->calc_studname)) !== null) {
            $student = Student::findOne($model->calc_studname);
        }

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                // если успешно, задаем сообщение об успешности
                Yii::$app->session->setFlash('success', 'Телефон клиента успешно изменен!');
            } else {
                // если не успешно задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Не удалось изменить телефон клиента!');
            }
            return $this->redirect(['studphone/create', 'sid' => $model->calc_studname]);
        } else {
            // выбираем телефоны клиента
            $phones = (new \yii\db\Query())
            ->select('id as id, calc_studname as sid, phone as phone, description as description')
            ->from('calc_studphone')
            ->where('visible=:vis', [':vis'=>1])
            ->andWhere(['calc_studname'=>$model->calc_studname])
            ->all();
            return $this->render('update', [
                'model' => $model,
                'student' => $student,
                'phones' => $phones,
                'userInfoBlock' => $userInfoBlock
            ]);
        }
    }

    /**
     * Удаляем телефон клиента.
     * При любом результате возвращаемся в метод Create
     * и задаем информационное сообщение.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        /* проверям роль пользователя */
        if(!Yii::$app->session->get('user.ustatus') == 3 && !Yii::$app->session->get('user.ustatus') == 4) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        
        $model = $this->findModel($id);
        if($model !== NULL) {
            $model->visible = 0;
            if($model->save()) {
                Yii::$app->session->setFlash('success', "Телефон клиента успешно удален!");
            } else {
                Yii::$app->session->setFlash('error', "Не удалось удалить телефон клиента!");
            }
        }
        
        return $this->redirect(['studphone/create', 'sid' => $model->calc_studname]);
    }

    /**
     * Finds the Studphone model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Studphone the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Studphone::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
