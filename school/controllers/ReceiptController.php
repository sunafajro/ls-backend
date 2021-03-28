<?php

namespace school\controllers;

use Yii;
use school\models\AccessRule;
use school\models\Receipt;
use school\models\Student;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Class ReceiptController
 * @package school\controllers
 */
class ReceiptController extends Controller
{
    /** {@inheritDoc} */
    public function behaviors(): array
    {
        $actions = ['index', 'common', 'create', 'delete', 'download-receipt'];
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => $actions,
                'rules' => [
                    [
                        'actions' => $actions,
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => $actions,
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

    /** {@inheritDoc} */
    public function beforeAction($action)
	{
		if(parent::beforeAction($action)) {
			if (AccessRule::checkAccess($action->controller->id, $action->id) === false) {
				throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
			}
			return true;
		} else {
			return false;
		}
    }

    /**
     * @param $sid
     * @return mixed
     */
    public function actionIndex($sid)
    {
        $this->layout = 'main-2-column';
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
        ]);
    }

    /**
     * @param $sid
     * @return mixed
     */
    public function actionCreate($sid)
    {
        $receipt = new Receipt();
        if ($receipt->load(Yii::$app->request->post())) {
            $oldSum = $receipt->sum;
            $receipt->sum = $receipt->sum * 100;
            $receipt->student_id = $sid;
            // добавляем id клиента для идентификации
            $receipt->purpose = "{$receipt->purpose}. Клиент №{$sid}.";
            $receipt->qrdata = Receipt::receiptParamsStringified() . '|';
            $receipt->qrdata .= Receipt::RECEIPT_LASTNAME . '=' . mb_strtoupper($receipt->name)    . '|';
            $receipt->qrdata .= Receipt::RECEIPT_PURPOSE  . '=' . mb_strtoupper($receipt->purpose) . '|';
            $receipt->qrdata .= Receipt::RECEIPT_SUM      . '=' . $receipt->sum;
            if ($receipt->save()) {
                Yii::$app->session->setFlash('success', "Квитанция успешно добавлена!");
            } else {
                $receipt->sum = $oldSum;
                Yii::$app->session->setFlash('error', "Не удалось добавить квитанцию!");
            }
        }
        return $this->redirect(['receipt/index', 'sid' => $sid]);
    }

    /**
     * @return mixed
     */
    public function actionCommon()
    {
        $this->layout = 'main-2-column';
        $model = new Receipt();

        return $this->render('common', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $receipt = Receipt::findOne(intval($id));
        if ($receipt !== NULL) {
            if ($receipt->delete()) {
                Yii::$app->session->setFlash('success', "Квитанция успешно удалена!");
            } else {
                Yii::$app->session->setFlash('error', "Ошибка удаления квитанции!");
            }
            return $this->redirect(['receipt/index', 'sid' => $receipt->student_id]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param string|null $id
     * @return mixed
     */
    public function actionDownloadReceipt(string $id = null)
    {
        $this->layout = 'print';
        $model = new Receipt();
        $receipt = [];
        if ($id) {
            $receipt = $model->getReceipt(intval($id));
            $receipt['sum'] = $receipt['sum'] / 100;
        } else {
            $model->load(Yii::$app->request->get());
            $receipt['payer']   = $model->payer ?? '';
            $receipt['sum']     = $model->sum ?? 0;
            $receipt['purpose'] = $model->purpose ?? '';
            $receipt['qrdata']  = join('|', [
                Receipt::receiptParamsStringified(),
                Receipt::RECEIPT_LASTNAME . '=' . mb_strtoupper($receipt['payer']),
                Receipt::RECEIPT_PURPOSE . '=' . mb_strtoupper($receipt['purpose']),
                Receipt::RECEIPT_SUM . '=' . $receipt['sum'] * 100
            ]);
        }
           
        return $this->render('_viewPdf', [
            'receipt'  => $receipt,
        ]);
    }
}
