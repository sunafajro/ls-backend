<?php

namespace app\controllers;

use Yii;
use app\models\AccessRule;
use app\models\Receipt;
use app\models\Student;
use app\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ReceiptController implements the CRUD actions for receipt model.
 */
class ReceiptController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'delete', 'download-receipt'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'delete', 'download-receipt'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create', 'delete', 'download-receipt'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
	{
		if(parent::beforeAction($action)) {
            $rule = new AccessRule();
			if ($rule->checkAccess($action->controller->id, $action->id) === false) {
				throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
			}
			return true;
		} else {
			return false;
		}
    }

    /**
     * Lists all Receipt models.
     * @return mixed
     */
    public function actionIndex($sid)
    {
        $student = Student::findOne($sid);
        $receipt = new Receipt();
        $receipt->name = $student->name;
        $contracts = $student->contracts ?? [];

        return $this->render('index', [
            'formReceiptData' => Receipt::receiptFormParams(),
            'receipt'         => $receipt,
            'receipts'        => $receipt->getReceipts(intval($sid)),
            'student'         => $student,
            'contract'        => array_pop($contracts),
            'userInfoBlock'   => User::getUserInfoBlock(),
        ]);
    }

    public function actionCreate($sid)
    {
        $receipt = new Receipt();
        if ($receipt->load(Yii::$app->request->post())) {
            $sum = str_replace(',', '.', $receipt->sum);
            $receipt->sum = $sum * 100;
            $receipt->student_id = $sid;
            // добавляем id клиента для идентификации
            $receipt->purpose = $receipt->purpose . '. Клиент №' . $sid . '.';
            $receipt->qrdata = Receipt::receiptParamsStringified() . '|';
            $receipt->qrdata .= Receipt::RECEIPT_LASTNAME . '=' . mb_strtoupper($receipt->name)    . '|';
            $receipt->qrdata .= Receipt::RECEIPT_PURPOSE  . '=' . mb_strtoupper($receipt->purpose) . '|';
            $receipt->qrdata .= Receipt::RECEIPT_SUM      . '=' . $receipt->sum;
            if ($receipt->save()) {
                Yii::$app->session->setFlash('success', "Квитанция успешно добавлена!");
            } else {
                Yii::$app->session->setFlash('error', "Не удалось добавить квитанцию!");
            }
        }
        return $this->redirect(['receipt/index', 'sid' => $sid]);
    }

    public function actionDelete($id)
    {
        $receipt = Receipt::findOne(intval($id));
        if ($receipt !== NULL) {
            if ((int)$receipt->visible === 1) {
                $receipt->visible = 0;
                if ($receipt->save()) {
                    Yii::$app->session->setFlash('success', "Квитанция успешно удалена!");
                } else {
                    Yii::$app->session->setFlash('error', "Ошибка удаления квитанции!");
                }
            } else {
                Yii::$app->session->setFlash('error', "Квитанция не является действующей!");
            }
            return $this->redirect(['receipt/index', 'sid' => $receipt->student_id]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDownloadReceipt($id)
    {
        $this->layout = 'print';
        $model = new Receipt();
        $receipt = $model->getReceipt(intval($id));
        if ($receipt) {                
            return $this->render('_viewPdf', [
                'receipt'  => $receipt,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
