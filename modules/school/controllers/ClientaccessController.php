<?php

namespace app\modules\school\controllers;

use Yii;
use app\models\ClientAccess;
use app\models\Student;
use app\modules\school\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

/**
 * ClientaccessController implements the CRUD actions for ClientAccess model.
 */
class ClientaccessController extends Controller
{
    public function behaviors()
    {
        $rules = ['create','update','enable'];
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
                    'enable' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param integer $sid
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCreate($sid)
    {
        $student = Student::findOne($sid);
        if (empty($student)) {
            throw new NotFoundHttpException("Студент №{$sid} не найден.");
        }
        if (in_array(Yii::$app->session->get('user.ustatus'), [3,4])) {
            $model = new ClientAccess();
            if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
                $model->id = $sid;
                $model->calc_studname = $sid;
                $model->date = date('Y-m-d');
                $model->site = 1;
                $model->password = md5($model->password);
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Логин и пароль к ЛК успешно созданы.");
                    return $this->redirect(['studname/view', 'id' => $model->calc_studname]);
                } else {
                    Yii::$app->session->setFlash('error', "Не удалось создать логин и пароль к ЛК.");
                }
            }
            return $this->render('create', [
                'model'         => $model,
                'student'       => $student,
                'loginStatus'   => $student->getStudentLoginStatus(),
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
	    } else {
            throw new ForbiddenHttpException('Доступ к данной странице ограничен.');
        }
    }

    /**
     * @param integer $sid
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($sid)
    {
        $student = Student::findOne($sid);
        if (empty($student)) {
            throw new NotFoundHttpException("Студент №{$sid} не найден.");
        }
        if (in_array(Yii::$app->session->get('user.ustatus'), [3,4])) {
            $model = $student->studentLogin;
            if (empty($model)) {
                throw new NotFoundHttpException("Логин студента №{$sid} не найден.");
            }
    	    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
                $model->password = md5($model->password);
                if ($model->save(true, ['username', 'password'])) {
                    Yii::$app->session->setFlash('success', "Логин и пароль к ЛК успешно изменены.");
                    return $this->redirect(['studname/view', 'id' => $model->calc_studname]);
                } else {
                    Yii::$app->session->setFlash('error', "Не удалось изменить логин или пароль к ЛК.");
                }
            }
            return $this->render('update', [
                    'model'         => $model,
                    'student'       => $student,
                    'loginStatus'   => $student->getStudentLoginStatus(),
                    'userInfoBlock' => User::getUserInfoBlock(),
            ]);
        } else {
            throw new ForbiddenHttpException('Доступ к данной странице ограничен.');
        }
    }

    /**
     * @param $sid
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionEnable($sid)
    {
        $student = Student::findOne($sid);
        if (empty($student)) {
            throw new NotFoundHttpException("Студент №{$sid} не найден.");
        }
        if (in_array(Yii::$app->session->get('user.ustatus'), [3,4])) {
            $model = $student->studentLogin;
            if (empty($model)) {
                throw new NotFoundHttpException("Логин студента №{$sid} не найден.");
            }
            $model->date = date('Y-m-d');
            if ($model->save(true, ['date'])) {
                Yii::$app->session->setFlash('success', "Вход в ЛК успешно восстановлен.");
            } else {
                Yii::$app->session->setFlash('error', "Не удалось восстановить вход в ЛК.");
            }
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            throw new ForbiddenHttpException('Доступ к данной странице ограничен.');
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
