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
                'only' => ['index'.'create','delete','enable','disable','done','undone','remain','unremain','get-data'],
                'rules' => [
                    [
                        'actions' => ['index'.'create','delete','enable','disable','done','undone','remain','unremain','get-data'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index'.'create','delete','enable','disable','done','undone','remain','unremain','get-data'],
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
        /* достаем данные поп студенту */
        $student   = Student::findOne($sid);

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
                'Если необходимо использовать скидку, то перед выставлением счета эту скидку нужно заранее добавить студенту (кроме постоянной.).'
            ];

            $sales = Salestud::getClientSalesSplited($sid);

            $labels = [
                'select' => Yii::t('app', '-select-'),
                'service' => Yii::t('app', 'Service'),
                'rubsale' => Yii::t('app', 'Ruble sale'),
                'rubsaleid' => Yii::t('app', 'Ruble sale (assigned)'),
                'rubsaleval' => Yii::t('app', 'Ruble sale (manual)'),
                'procsale' => Yii::t('app', 'Procent sale'),
                'permsale' => Yii::t('app', 'Permament sale'),
                'num' => Yii::t('app', 'Lesson count'),
                'remain' => Yii::t('app', 'Remain'),
                'office' => Yii::t('app', 'Office'),
                'calculate' => Yii::t('app', 'Calculate'),
                'addsale' => Yii::t('app', 'Add'),
                'total' => Yii::t('app', 'Invoice cost'),
                'sendingMessage' => Yii::t('app', 'Sending invoice data to server...'),
                'saveErrorMessage' => Yii::t('app', 'Failed to save the invoice on server!'),
                'saveSuccessMessage' => Yii::t('app', 'The invoice successfully saved on server!'),
            ];
            return [
                'userData' => User::getUserInfo(),
                'hints' => $hints,
                'services' => Service::getInvoiceServicesList(),
                'rubsales'  => $sales['rub'],
                'procsales' => $sales['proc'],
                'offices' => (int)Yii::$app->session->get('user.ustatus') !== 4 ? Office::getOfficesList() : [],
                'permsale'  => Salestud::getClientPermamentSale($sid),
                'labels' => $labels
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
        if(Yii::$app->request->post('Invoicestud')) {
            $data = Yii::$app->request->post('Invoicestud');
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

            if ($model->calc_salestud === 0 && (int)$data['rubsalesval'] !== 0) {
              $model->calc_salestud = Salestud::applyRubSale((float)$data['rubsalesval'], $model->calc_studname);
            }

            /* обновляем информацию по использованию рублевой скидки */
            if ($model->calc_salestud > 0) {
                $rsalestud = Salestud::findOne($model->calc_salestud);
                $rsalestud->user_used = Yii::$app->session->get('user.uid');
                $rsalestud->data_used = date('Y-m-d');
                $rsalestud->save();
            }

            /* обновляем информацию по использованию процентной скидки */
            if ($model->calc_salestud_proc > 0) {
                $psalestud = Salestud::findOne($model->calc_salestud_proc);
                $psalestud->user_used = Yii::$app->session->get('user.uid');
                $psalestud->data_used = date('Y-m-d');
                $psalestud->save();
            }

            if($model->save()) {
                /* обновляем карточку студента */
                $student = Student::updateInvMonDebt((int)$data['sid']);
                return true;
            } else {
                Yii::$app->response->statusCode = 500;
                return false;
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'response' => 'bad_request',
                'message' => Yii::t('yii', 'Missing required parameters: { Invoicestud }')
            ];
        }
    }

    /**
     * Метод позволяет менеджерам и руководителям, перевести счета в состояние - отработанные.
     * Для перевода необходим id счета.
     */
    public function actionDone($id)
    {
        $model = $this->findModel($id);
        // проверяем что счет не отработан
        if($model->done != 1) {
            // помечаем счет как отработанный
            $model->done = 1;
            // указываем пользователя который перевел счет в состояние - отработан
            $model->user_done = Yii::$app->session->get('user.uid');
            // указываем дату перевода счета в состояние - отработан
            $model->data_done = date('Y-m-d');
            // если запись успешно сохранилась
            $model->save();
            // возвращаемся в карточку
            return $this->redirect(['studname/view', 'id' => $model->calc_studname, 'tab'=>3]);
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Метод позволяет менеджерам и руководителям, перевести счета в состояние - рабочие.
     * Для перевода необходим id счета.
     */
    public function actionUndone($id)
    {
        $model = $this->findModel($id);
        // проверяем что счет отработан
        if($model->done != 0) {
            // помечаем счет как рабочий
            $model->done = 0;
            // указываем пользователя который перевел счет в состояние - рабочие
            $model->user_done = Yii::$app->session->get('user.uid');
            // указываем дату перевода счета в состояние - рабочие
            $model->data_done = date('Y-m-d');
            // если запись успешно сохранилась
            $model->save();
            // возвращаемся в карточку
            return $this->redirect(['studname/view', 'id' => $model->calc_studname, 'tab'=>3]);
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Метод позволяет менеджерам и руководителям, аннулировать счета выставленные клиенту.
     * Для аннулирования необходим id счета. 
     * Стоимость счета вычитается из общей суммы счетов, долг клиента пересчитывается.
     */
    public function actionDisable($id)
    {
        $model = $this->findModel($id);
        // проверяем что счет действующий
        if($model->visible != 0) {
            // помечаем счет как аннулированные
            $model->visible = 0;
            // указываем пользователя который аннулировал счет
            $model->user_visible = Yii::$app->session->get('user.uid');
            // указываем дату аннулирования счета
            $model->data_visible = date('Y-m-d');
            // если запись успешно сохранилась
            if($model->save()){
                // находим карточку клиента
                $student = Student::updateInvMonDebt($model->calc_studname);               
                // задаем сообщение об успешном аннулировании счета
                Yii::$app->session->setFlash('success', "Счет успешно аннулирован!");
            } else {
                // задаем сообщение об безуспешном аннулировании счета
                Yii::$app->session->setFlash('error', "Не удалось аннулировать счет!");
            }
            // возвращаемся в карточку
            return $this->redirect(['studname/view', 'id' => $model->calc_studname, 'tab'=>3]);
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Метод позволяет менеджерам и руководителям, восстановить аннулированные счета выставленные клиенту.
     * Для аннулирования необходим id счета. 
     * Стоимость счета добавляется в общую сумму счетов, долг клиента пересчитывается.
     */
    public function actionEnable($id)
    {
        $model = $this->findModel($id);
        // проверяем что счет аннулирован
        if($model->visible != 1) {
            // помечаем счет как действующий
            $model->visible = 1;
            // указываем пользователя который восстановил счет
            $model->user_visible = Yii::$app->session->get('user.uid');
            // указываем дату восстановления счета
            $model->data_visible = date('Y-m-d');
            // если запись успешно сохранилась
            if($model->save()){
                // находим карточку клиента
                $student = Student::updateInvMonDebt($model->calc_studname);                
                // задаем сообщение об успешном восстановлении счета
                Yii::$app->session->setFlash('success', "Счет успешно восстановлен!");
            } else {
                // задаем сообщение об безуспешном восстановлении счета
                Yii::$app->session->setFlash('error', "Не удалось восстановить счет!");
            }
            // возвращаемся в карточку
            return $this->redirect(['studname/view', 'id' => $model->calc_studname, 'tab'=>3]);
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
    * метод позволяет менеджерам и руководителям 
    * перевести тип счета клиента в "остаточный". Для этого необходим ID счета.
    **/

    public function actionRemain($id)
    {
        $model = $this->findModel($id);
        // проверяем что счет действующий и не остаточный
        if($model->visible==1 && $model->remain !=1 ) {
            // помечаем счет как остаточный
            $model->remain = 1;
            // сохраняем
            if($model->save()) {
                // если успешно, задаем сообщение об успешности
                Yii::$app->session->setFlash('success', 'Счет успешно переведен в статус "Остаточный"!');
            } else {
                // если не успешно задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Не удалось перевести счет в статус "Остаточный"!');
            }
        }
        // возвращаемся обратно
        return $this->redirect(Yii::$app->request->referrer); 
    }

    /**
    * метод позволяет менеджерам и руководителям 
    * перевести тип счета клиента в "нормальный". Для этого необходим ID счета.
    **/

    public function actionUnremain($id)
    {
        $model = $this->findModel($id);
        // проверяем что счет действующий и остаточный
        if($model->visible==1 && $model->remain !=0) {
            // помечаем счет как обычный
            $model->remain = 0;
            // сохраняем
            if($model->save()) {
                // если успешно, задаем сообщение об успешности
                Yii::$app->session->setFlash('success', 'Счет успешно переведен в статус "Обычный"!');
            } else {
                // если не успешно задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Не удалось перевести счет в статус "Обычный"');
            }
        }
        // возвращаемся обратно
        return $this->redirect(Yii::$app->request->referrer); 
    }

    /**
     * Deletes an existing CalcInvoicestud model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // находим обьект
        $model = $this->findModel($id);
        //делаем копию обекта для возврата
        $tmp = $model;
        // проверяем что счет действующий
        if($model->visible != 1) {
            // удаляем запись
            if($model->delete()) {
                // задаем сообщение об успешном удалении счета
                Yii::$app->session->setFlash('success', "Счет успешно удален!");
            } else {
                // задаем сообщение о безуспешном удалении счета
                Yii::$app->session->setFlash('success', "Не удалось удалить счет!");
            }
        }
        // возвращаемся обратно
        return $this->redirect(Yii::$app->request->referrer);
    }

    // public function actionGetsale() {
    //     /* включаем формат ответа JSON */
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     $data = Yii::$app->request->post('data');
    //     if ($data) {
    //         $rub = $data['rub'];
    //         $proc = $data['proc'];
    //         $perm = $data['perm'];
    //         $service = $data['serv'];
    //         $num = $data['num'];
    //         if($rub || $proc) {
    //             $sales_id = [];
    //             /* заполняем массив указанными скидками */
    //             if ($rub) {
    //                 $sales_id[] = $rub;
    //             }
    //             if ($proc) {
    //                 $sales_id[] = $proc;
    //             }
    //             $lessonsum = Service::getServiceValue($service);
    //             $totalsum  = $lessonsum * $num;
    //             /* считаем сумму скидки */
    //             $salesum = Salestud::calculateInvoiceSale($sales_id, $perm, $totalsum);

    //             return [
    //                 'response' => 'success',
    //                 'sale' => $salesum['type'],
    //                 'message' => Yii::t('app', 'Sale was successfully calculated.'),
    //             ];
    //         } else if($perm) {
    //             return [
    //                 'response' => 'success',
    //                 'sale' => 'perm',
    //                 'message' => Yii::t('app', 'Sale was successfully calculated.'),
    //             ];
    //         } else {
    //             return [
    //                 'response' => 'error',
    //                 'sale' => null,
    //                 'message' => Yii::t('app', 'Failed to calculate Sale.'),
    //             ];
    //         }

    //     } else {
    //         Yii::$app->response->statusCode = 404;
    //         return [
    //             'response' => 'not_found',
    //             'message' => Yii::t('yii', 'The requested sale does not exist.')
    //         ];
    //     }
    // }

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
	
	/*
	* Метод высчитывает долг клиента и возвращает значение
 	*/
	
	// protected function studentDebt($id) {
		
	// 	// задаем переменную в которую будет подсчитан долг по занятиям
	// 	$debt_lessons = 0;
	// 	// задаем переменную в которую будет подсчитан долг по разнице между счетами и оплатами
	// 	$debt_common = 0;
	// 	// полный долг
	// 	$debt = 0;
		
	// 	// получаем информацию по счетам
	// 	$invoices_sum = (new \yii\db\Query())
 //        ->select('sum(value) as money')
 //        ->from('calc_invoicestud')
	// 	->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
 //        ->one();
		
	// 	// получаем информацию по оплатам
	// 	$payments_sum = (new \yii\db\Query())
 //        ->select('sum(value) as money')
 //        ->from('calc_moneystud')
	// 	->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
 //        ->one();
		
	// 	// считаем разницу как базовый долг
	// 	$debt_common = $payments_sum['money'] - $invoices_sum['money'];
		
	// 	// запрашиваем услуги назначенные студенту
	// 	$services = (new \yii\db\Query())
	// 	->select('s.id as sid, s.name as sname, SUM(is.num) as num')
	// 	->distinct()
	// 	->from('calc_service s')
	// 	->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
	// 	->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])
	// 	->andWhere(['is.calc_studname'=>$id])
	// 	->groupby(['is.calc_studname','s.id'])
	// 	->orderby(['s.id'=>SORT_ASC])
	// 	->all();
		
	// 	// проверяем что у студента есть назначенные услуги
	// 	if(!empty($services)){
	// 		$i = 0;
	// 		// распечатываем массив
	// 		foreach($services as $service){
	// 			// запрашиваем из базы колич пройденных уроков
	// 			$lessons = (new \yii\db\Query())
	// 			->select('COUNT(sjg.id) AS cnt')
	// 			->from('calc_studjournalgroup sjg')
	// 			->leftjoin('calc_groupteacher gt', 'sjg.calc_groupteacher=gt.id')
	// 			->leftjoin('calc_journalgroup jg', 'sjg.calc_journalgroup=jg.id')
	// 			->where('jg.view=:vis and jg.visible=:vis and (sjg.calc_statusjournal=:vis or sjg.calc_statusjournal=:stat) and gt.calc_service=:sid and sjg.calc_studname=:stid', [':vis'=>1, 'stat'=>3, ':sid'=>$service['sid'], ':stid'=>$id])
	// 			->one();

	// 			// считаем остаток уроков
	// 			$services[$i]['num'] = $services[$i]['num'] - $lessons['cnt'];
	// 			$i++;
	// 		}
	// 		// уничтожаем переменные
	// 		unset($service);
	// 		unset($lessons);
			
	// 		foreach($services as $s) {
 //                if($s['num'] < 0){
	// 					$lesson_cost = (new \yii\db\Query())
	// 					->select('(value/num) as money')
	// 					->from('calc_invoicestud')
	// 					->where('visible=:vis and calc_studname=:stid and calc_service=:sid', [':vis'=>1, ':stid'=>$id, ':sid'=>$s['sid']])
	// 					->orderby(['id'=>SORT_DESC])
	// 					->one();
						
	// 					$debt_lessons = $debt_lessons + $s['num'] * $lesson_cost['money'];
	// 			}				
	// 		}
	// 	}
	// 	unset($services);
	// 	$debt = $debt_common + $debt_lessons;
	// 	//$debt = number_format($debt, 1, '.', ' ');
	// 	return (int)$debt;
	// }
}
