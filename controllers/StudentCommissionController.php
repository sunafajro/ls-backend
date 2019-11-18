<?php

namespace app\controllers;

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
use yii\web\Response;

/**
 * StudentCommissionController implements the CRUD actions for StudentCommission model.
 */
class StudentCommissionController extends Controller
{
    /**
     * @param int $id
     */
    public function actionCreate(int $sid)
    {
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