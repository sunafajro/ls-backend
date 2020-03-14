<?php

namespace app\controllers;

use Yii;
use app\models\Invoicestud;
use app\models\Office;
use app\models\Student;
use app\models\Salestud;
use app\models\Service;
use app\models\Tool;
use app\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * InvoiceController implements the CRUD actions for CalcInvoicestud model.
 */
class InvoiceController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'index',
                    'create',
                    'delete',
                    'toggle',
                    'get-data',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'create',
                            'delete',
                            'toggle',
                            'get-data',
                        ],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'index',
                            'create',
                            'delete',
                            'toggle',
                            'get-data',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'get-data' => ['post']
                ],
            ],
        ];
    }

    /**
     * метод контролирует доступ к экшенам контроллера
     * @param array $action
     * @return boolean
     */
    public function beforeAction($action)
	{
		if(parent::beforeAction($action)) {
			if (User::checkAccess($action->controller->id, $action->id) == false) {
				throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
			}
			return true;
		} else {
			return false;
		}
	}

    /**
     * метод возвращает страницу с формой добавления счета
     * @param {integer} $sid 
     */
    public function actionIndex($sid)
    {
        $student = Student::findOne($sid);
        return $this->render('index', [
            'student' => $student
        ]);
    }

    /**
     * метод возвращает данные для страницы с формой добавления счета
     * @return array 
     */
    public function actionGetData()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->post('sid')) {
            $sid = Yii::$app->request->post('sid');

            $hints = [
                'Счет помечается "остаточным", если необходимо указать какие то занятия, которые были проведены, на момент ввода остатков и Студент школе за них должен.',
                'Если необходимо использовать скидку, то перед выставлением счета эту скидку нужно заранее добавить студенту (кроме постоянной).',
                'При указании отрицательной суммы в поле рублевой скидки, она превращается в надбавку и позволяет корректировать счет в большую сторону.'
            ];

            $sales = Salestud::getClientSalesSplited($sid);

            $labels = [
                'select' => Yii::t('app', '-select-'),
                'service' => Yii::t('app', 'Service'),
                'rubsale' => Yii::t('app', 'Ruble sale'),
                'rubsaleid' => Yii::t('app', 'Ruble sale (assigned)'),
                'rubsaleval' => Yii::t('app', 'Ruble sale (manual)'),
                'procsale' => Yii::t('app', 'Percent sale'),
                'permsale' => Yii::t('app', 'Permament sale'),
                'num' => Yii::t('app', 'Lesson count'),
                'remain' => Yii::t('app', 'Remain'),
                'corp' => Yii::t('app', 'Corporative'),
                'office' => Yii::t('app', 'Office'),
                'calculate' => Yii::t('app', 'Calculate'),
                'addsale' => Yii::t('app', 'Add'),
                'total' => Yii::t('app', 'Invoice cost'),
                'sendingMessage' => Yii::t('app', 'Sending invoice data to server...'),
                'saveErrorMessage' => Yii::t('app', 'Failed to save the invoice on server!'),
                'saveSuccessMessage' => Yii::t('app', 'The invoice successfully saved on server!'),
                'salepurpose' => Yii::t('app', 'Reason'),
            ];
            $user = new User();
            return [
                'userData'  => $user->getUserInfo(),
                'hints'     => $hints,
                'services'  => Service::getInvoiceServicesList(),
                'rubsales'  => $sales['rub'],
                'procsales' => $sales['proc'],
                'offices'   => (int)Yii::$app->session->get('user.ustatus') !== 4 ? Office::getOfficesList() : [],
                'permsale'  => Salestud::getClientPermamentSale($sid),
                'labels'    => $labels
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'response' => 'bad_request',
                'message' => Yii::t('yii', 'Missing required parameters: {sid}')
            ];
        }
    }

    /**
     * Метод позволяет менеджерам и руководителям, выставить счет клиенту.
     * Для выставления счета необходим id клиента.
     * Стоимость счета добавляется в общую сумму счетов, долг клиента пересчитывается.
     */

    public function actionCreate()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->post('Invoicestud')) {
            $data = Yii::$app->request->post('Invoicestud');
            $student = Student::findOne((int)$data['sid']);
            if (empty($student)) {
                Yii::$app->response->statusCode = 404;
                return [
                    'response' => 'not_found',
                    'message'  => Yii::t('yii', 'Student not found.')
                ];
            }
            /* заполняем модель */
            $model = new Invoicestud();
            $model->visible             = 1;
            $model->data                = date('Y-m-d');
            $model->calc_service        = isset($data['service'])           ? (int)$data['service']                    : 0;
            $model->calc_studname       = isset($data['sid'])               ? (int)$data['sid']                        : 0;
            $model->calc_salestud       = isset($data['rubsaleid'])         ? (int)$data['rubsaleid']                  : 0;
            $model->calc_salestud_proc  = isset($data['procsale'])          ? (int)$data['procsale']                   : 0;
            $model->calc_sale           = isset($data['permsale'])          ? (int)$data['permsale']                   : 0;
            /* для менеджеров офис подставляется из сессии, для руководителей из формы */
            if((int)Yii::$app->session->get('user.ustatus') === 4) {
                $model->calc_office     = (int)Yii::$app->session->get('user.uoffice_id');
            } else {
                $model->calc_office     = isset($data['office'])            ? (int)$data['office']                     : 0;
            }
            $model->num                 = isset($data['num'])               ? (int)$data['num']                        : 0;
            $model->value               = isset($data['invoiceValue'])      ? (float)$data['invoiceValue']             : 0;
            $model->value_discount      = isset($data['invoiceDiscount'])   ? (float)$data['invoiceDiscount']          : 0;
            $model->user                = (int)Yii::$app->session->get('user.uid');
            $model->done                = 0;
            $model->data_done           = '0000-00-00';
            $model->data_visible        = '0000-00-00';
            $model->user_done           = 0;
            $model->user_visible        = 0;
            $model->cumdisc             = 0;
            $model->cumdisc_name        = '';
            $model->remain              = isset($data['remain'])            ? (int)$data['remain']                     : 0;
            $model->user_remain         = $model->remain > 0                ? (int)Yii::$app->session->get('user.uid') : 0;
            $model->data_remain         = $model->remain > 0                ? date('Y-m-d')                            : '0000-00-00';

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->calc_salestud === 0 && (int)$data['rubsalesval'] !== 0 && $data['salePurpose']) {
                    $saleId = Salestud::applyRubSale((float)$data['rubsalesval'], $model->calc_studname, $data['salePurpose']);
                    if ($saleId === 0) {
                        throw new ServerErrorHttpException('Не удалось применить рублевую скидку.');
                    }
                    $model->calc_salestud = $saleId;
                }

                if ($model->calc_salestud > 0) {
                    $rsalestud = Salestud::findOne($model->calc_salestud);
                    $rsalestud->user_used = Yii::$app->session->get('user.uid');
                    $rsalestud->data_used = date('Y-m-d');
                    if (!$rsalestud->save(true, ['user_used', 'data_used'])) {
                        throw new ServerErrorHttpException('Не удалось обновить использование рублевой скидки.');
                    }
                }

                if ($model->calc_salestud_proc > 0) {
                    $psalestud = Salestud::findOne($model->calc_salestud_proc);
                    $psalestud->user_used = Yii::$app->session->get('user.uid');
                    $psalestud->data_used = date('Y-m-d');
                    if (!$psalestud->save(true, ['user_used', 'data_used'])) {
                        throw new ServerErrorHttpException('Не удалось обновить использование процентной скидки.');
                    }
                }

                if (!$model->save()) {
                    throw new ServerErrorHttpException('Не удалось сохранить счет.');
                }

                if (!$student->updateInvMonDebt()) {
                    throw new ServerErrorHttpException('Не удалось обновить баланс клиента.');
                }

                $transaction->commit();
                return [
                    'response' => 'success',
                    'message'  => 'Счет успешно сохранен.',
                ];
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->response->statusCode = 500;
                return [
                    'response' => 'server_error',
                    'message'  => $e->getMessage(),
                ];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'response' => 'bad_request',
                'message'  => Yii::t('yii', 'Missing required parameters: { Invoicestud }.')
            ];
        }
    }

    public function actionToggle(string $id, string $action)
    {
        $invoice = $this->findModel($id);
        if($invoice !== NULL) {
            switch($action) {
                case 'corp': {
                    if ((int)$invoice->calc_office !== 6) {
                        $invoice->calc_office = 6;
                    } else {
                        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
                            $invoice->calc_office = Yii::$app->session->get('user.uoffice_id');
                        } else {
                            Yii::$app->session->setFlash('error', 'Возврат счета в обычное состояние доступен только менеджерам!');
                        }
                    }
                    if ($invoice->save()) {
                        Yii::$app->session->setFlash('success', 'Счет успешно изменен!');
                    } else {
                        Yii::$app->session->setFlash('error', 'Не удалось изменить счет!');
                    }
                    break;
                }
                case 'done': {
                    $invoice->done = (int)$invoice->done === 0 ? 1 : 0;
                    $invoice->user_done = (int)$invoice->done === 1 ? Yii::$app->session->get('user.uid') : 0;
                    $invoice->data_done = (int)$invoice->done === 1 ? date('Y-m-d') : '0000-00-00';
                    if ($invoice->save()) {
                        Yii::$app->session->setFlash('success', 'Счет успешно изменен!');
                    } else {
                        Yii::$app->session->setFlash('error', 'Не удалось изменить счет!');
                    }
                    break;
                }
                case 'enable': {
                    $invoice->visible = (int)$invoice->visible === 0 ? 1 : 0;
                    $invoice->user_visible = (int)$invoice->visible === 1 ? 0 : Yii::$app->session->get('user.uid');
                    $invoice->data_visible = (int)$invoice->visible === 1 ? '0000-00-00' : date('Y-m-d');
                    // TODO transaction
                    if ($invoice->save()) {
                        $student = Student::findOne($invoice->calc_studname);
                        if ($student !== NULL) {
                            $student->updateInvMonDebt();
                        }
                        Yii::$app->session->setFlash('success', (int)$invoice->visible === 1 ? 'Счет успешно восстановлен!' : 'Счет успешно аннулирован!');
                    } else {
                        Yii::$app->session->setFlash('error', (int)$invoice->visible === 1 ? 'Не удалось восстановить счет!' : 'Не удалось аннулировать счет!');
                    }
                    break;
                }
                case 'netting': {
                    $invoice->remain = (int)$invoice->remain === 0 ? 2 : 0;
                    $invoice->user_remain = (int)$invoice->remain === 0 ? 0 : Yii::$app->session->get('user.uid');
                    $invoice->data_remain = (int)$invoice->remain === 0 ? '0000-00-00' : date('Y-m-d');
                    // TODO transaction
                    if($invoice->save()) {
                        $student = Student::findOne($invoice->calc_studname);
                        if ($student !== NULL) {
                            $student->updateInvMonDebt();
                        }
                        Yii::$app->session->setFlash('success', 'Счет успешно изменен!');
                    } else {
                        Yii::$app->session->setFlash('error', 'Не удалось изменить счет!');
                    }
                    break;
                }
                case 'remain': {
                    $invoice->remain = (int)$invoice->remain === 0 ? 1 : 0;
                    $invoice->user_remain = (int)$invoice->remain === 0 ? 0 : Yii::$app->session->get('user.uid');
                    $invoice->data_remain = (int)$invoice->remain === 0 ? '0000-00-00' : date('Y-m-d');
                    if($invoice->save()) {
                        Yii::$app->session->setFlash('success', 'Счет успешно изменен!');
                    } else {
                        Yii::$app->session->setFlash('error', 'Не удалось изменить счет!');
                    }
                    break;
                }
            }
            return $this->redirect(['studname/view', 'id' => $invoice->calc_studname, 'tab' => 3]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Invoice not found!'));
        }
    }

    public function actionDelete(string $id)
    {
        $model = $this->findModel($id);
        if ($model !== NULL) {
            $tmp = $model;
            if ((int)$model->visible !== 1) {
                if($model->delete()) {
                    Yii::$app->session->setFlash('success', "Счет успешно удален!");
                } else {
                    Yii::$app->session->setFlash('success', "Не удалось удалить счет!");
                }
            }
            return $this->redirect(['studname/view', 'id' => $tmp->calc_studname, 'tab' => 3]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Invoice not found!'));
        }
    }

    /**
     * Finds the CalcInvoicestud model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcInvoicestud the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoicestud::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
