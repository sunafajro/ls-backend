<?php

namespace app\controllers;

use Yii;
use app\models\AccessRule;
use app\models\Contract;
use app\models\Student;
use app\models\Tool;
use app\models\User;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * CintractController implements the CRUD actions for Contract model.
 */
class ContractController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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

    public function beforeAction($action)
    {
        if(parent::beforeAction($action)) {
            if (AccessRule::CheckAccess($action->controller->id, $action->id) === false) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            return true;
        } else {
            return false;
        }
    }

    public function actionCreate($sid)
    {
        $client = Student::findOne($sid);
        if (!$client) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $contracts = Contract::getClientContracts($sid);
        $model = new Contract();
        if ($model->load(Yii::$app->request->post())) {
            $model->calc_studname = $sid;
            if($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app','Договор успешно добавлен!'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app','Не удалось добавить договор!'));
            }
            return $this->redirect(['contract/create', 'sid' => $sid]);
        }
        return $this->render('create', [
            'client' => $client,
            'contracts' => $contracts,
            'model' => $model,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    public function actionDelete($id)
    {
        $contract = $this->findModel($id);
        $sid = $contract->calc_studname;
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