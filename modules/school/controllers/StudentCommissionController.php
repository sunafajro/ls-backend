<?php

namespace app\modules\school\controllers;

use Yii;
use app\models\Office;
use app\models\Student;
use app\models\StudentCommission;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * StudentCommissionController implements the CRUD actions for StudentCommission model.
 */
class StudentCommissionController extends Controller
{
    public function behaviors()
    {
        $rules = ['create', 'delete'];
        return [
	        'access' => [
                'class' => AccessControl::class,
                'only' => $rules,
                'rules' => [
                    [
                        'actions' => $rules,
                        'allow'   => false,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => $rules,
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param int $id
     */
    public function actionCreate(int $sid)
    {
        $roleId = (int)Yii::$app->session->get('user.ustatus');
        if (!in_array($roleId, [3, 4])) {
            throw new ForbiddenHttpException('Access denied');
        }
        $student = Student::findOne($sid);
        if (empty($student)) {
            throw new NotFoundHttpException("Студент №{$sid} не найден.");
        }
        $request = Yii::$app->request;
        $model = new StudentCommission([
            'date'    => date('Y-m-d'),
            'percent' => StudentCommission::COMMISSION_PERCENT,
            'debt'    => $student->debt,
            'value'   => round(abs($student->debt * StudentCommission::COMMISSION_PERCENT / 100))
        ]);
        if ($request->isPost && $model->load($request->post())) {
            $model->student_id = $sid;
            if ($roleId === 4) {
                $model->office_id = Yii::$app->session->get('user.uoffice_id');
            }
            if ($model->save()) {
                if ($student->updateInvMonDebt()) {
                    Yii::$app->session->setFlash('success', 'Комиссия успешно добавлена.');
                } else {
                    Yii::$app->session->setFlash('error', 'Комиссия добавлена, но не удалось пересчитать баланс клиента.');
                }
                
                return $this->redirect(['studname/view', 'id' => $sid, 'tab' => 5]);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить комиссию.');
            }
        }

        return $this->render('create', [
            'model'         => $model,
            'offices'       => ArrayHelper::map((new Office())->getOffices(), 'id', 'name'),
            'student'       => $student,
            'userInfoBlock' => User::getUserInfoBlock()
        ]);
    }

    public function actionDelete(int $id)
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4])) {
            throw new ForbiddenHttpException('Access denied');
        }
        $model = $this->findModel($id);
        $student = Student::findOne($model->student_id);
        if (empty($student)) {
            throw new NotFoundHttpException("Студент №{$model->student_id} не найден.");
        }
        if ($model->delete()) {
            if ($student->updateInvMonDebt()) {
                Yii::$app->session->setFlash('success', 'Комиссия успешно удалена.');
            } else {
                Yii::$app->session->setFlash('error', 'Комиссия удалена, но не удалось пересчитать баланс клиента.');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the StudentCommission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StudentCommission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudentCommission::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}