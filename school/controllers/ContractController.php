<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\Contract;
use school\models\Student;
use school\models\User;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * CintractController implements the CRUD actions for Contract model.
 */
class ContractController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'delete'],
                'rules' => [
                    [
                        'actions' => ['create', 'delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate($sid)
    {
        $student = Student::findOne($sid);
        if (!$student) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model = new Contract();
        if ($model->load(Yii::$app->request->post())) {
            $model->student_id = $sid;
            if($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app','Договор успешно добавлен!'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app','Не удалось добавить договор!'));
            }
            return $this->redirect(['contract/create', 'sid' => $sid]);
        }

        return $this->render('create', [
            'student'       => $student,
            'contracts'     => $student->contracts ?? [],
            'model'         => $model,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    public function actionDelete($id)
    {
        $contract = $this->findModel($id);
        $sid = $contract->student_id;
        if ($contract->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app','Договор успешно удален!'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app','Не удалось удалить договор!'));
        }
        return $this->redirect(['contract/create', 'sid' => $sid]);
    }

    protected function findModel($id)
    {
        if (($model = Contract::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}