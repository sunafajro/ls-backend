<?php

namespace app\modules\school\controllers;

use Yii;
use app\models\Moneystud;
use app\models\Notification;
use app\models\Office;
use app\models\Student;
use app\modules\school\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * MoneystudController implements the CRUD actions for Moneystud model.
 */
class MoneystudController extends Controller
{
    public function behaviors()
    {
        $rules = ['create', 'delete', 'disable','enable', 'remain', 'unremain', 'autocomplete'];
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
                    'delete' => ['post'],
                    'autocomplete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Метод позволяет менеджерам и руководителям
     * создавать оплаты клиента. Для создания оплаты необходим ID клиента.
     * @param int|null $sid
     * @param int|null $oid
     * 
     * @return mixed
     */

    public function actionCreate(int $sid = NULL, int $oid = NULL)
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4, 11])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        // создаем пустую модель записи об оплате
        $model = new Moneystud();

        // находим информацию по клиенту
        $student = Student::findOne($sid);

        $office = new Office();
        $offices = ArrayHelper::map($office->getOffices(), 'id', 'name');

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if (!$sid) {
                $student = Student::findOne($model->calc_studname);
            } else {
                // указываем id клиента
                $model->calc_studname = $sid;
            }
            if (!$student) {
                $model->addError('calc_studname', Yii::t('app', 'Student must be selected!'));
                return $this->render('create', [
                    'model'         => $model,
                    'offices'       => $offices,
                    'student'       => $student,
                    'userInfoBlock' => User::getUserInfoBlock()
                ]);
            }
            $model->value_cash      = $this->prepareInput((string)$model->value_cash);
            $model->value_card      = $this->prepareInput((string)$model->value_card);
            $model->value_bank      = $this->prepareInput((string)$model->value_bank);
            $model->value           = $model->value_cash + $model->value_card + $model->value_bank;
            $model->visible         = 1;
            $model->user            = Yii::$app->session->get('user.uid');
            $model->user_visible    = 0;
            $model->user_remain     = 0;
            $model->user_collection = 0;
            $model->collection      = 0;
            $model->data            = date('Y-m-d');
            $model->data_visible    = '0000-00-00';
            $model->data_remain     = '0000-00-00';
            $model->data_collection = '0000-00-00';
            // для менеджеров офис подставляем автоматом
            if ((int)Yii::$app->session->get('user.ustatus') === 4) {
                $model->calc_office = Yii::$app->session->get('user.uoffice_id');
            }
            // TODO create transaction
            if ($model->save()) {
                if (Yii::$app->request->post('sendEmail')) {
                    $notification            = new Notification();
                    $notification->entity_id = $model->id;
                    $notification->type      = Notification::TYPE_PAYMENT;
                    $notification->user_id   = Yii::$app->session->get('user.uid');
                    $notification->save();
                }
                if ($student->updateInvMonDebt()) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Payment successfully created!'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to create payment!'));
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to create payment!'));
            }
            return $this->redirect(['moneystud/create', 'sid' => $sid, 'oid' => $model->calc_office]);
        } else {
            $model->calc_office = Yii::$app->request->get('oid', NULL);
            return $this->render('create', [
                'model'         => $model,
                'oid'           => $oid,
                'offices'       => $offices,
                'payments'      => $model->getLastPaymentsByCreator(),
                'student'       => $student,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * метод позволяет менеджерам и руководителям 
     * аннулировать оплату клиента. Для аннулирования необходим ID оплаты.
     * @param int $id
     * 
     * @return mixed
     */
    public function actionDisable(int $id)
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        $model = $this->findModel($id);
        // проверяем что оплата действующая
        if ((int)$model->visible === 1) {
            $model->visible      = 0;
            $model->user_visible = Yii::$app->session->get('user.uid');
            $model->data_visible = date('Y-m-d');
            // уведомление об оплате
            $notification = Notification::find()->where([
                'entity_id' => $model->id,
                'type'      => Notification::TYPE_PAYMENT,
            ])->one();
            if ($notification !== NULL) {
                $notification->visible = 0;
                $notification->save(true, ['visible']);
            }
            // TODO create transaction
            if($model->save(true, ['visible', 'user_visible', 'data_visible'])) {
                $student = Student::findOne($model->calc_studname);
                $student->updateInvMonDebt();
                Yii::$app->session->setFlash('success', 'Оплата успешно аннулирована.');
            }
            return $this->redirect(['studname/view', 'id' => $student->id, 'tab'=>4]);
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer); 
        }
    }

    /**
     * метод позволяет менеджерам и руководителям 
     * восстановить оплату клиента. Для восстановления необходим ID оплаты.
     * @param int $id
     * 
     * @return mixed
     */    
    public function actionEnable(int $id)
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        $model = $this->findModel($id);
        // проверяем что оплата аннулирована
        if($model->visible==0) {
            $model->visible      = 1;
            $model->user_visible = Yii::$app->session->get('user.uid');
            $model->data_visible = date('Y-m-d');
            // уведомление об оплате
            $notification = Notification::find()->where([
                'entity_id' => $model->id,
                'type'      => Notification::TYPE_PAYMENT,
            ])->one();
            if ($notification !== NULL) {
                $notification->visible = 1;
                $notification->save(true, ['visible']);
            }
            // TODO create transaction
            if ($model->save(true, ['visible', 'user_visible', 'data_visible'])) {
                $student = Student::findOne($model->calc_studname);
                $student->updateInvMonDebt();
                Yii::$app->session->setFlash('success', 'Оплата успешно восстановлена.');
            }
            return $this->redirect(['studname/view', 'id' => $student->id, 'tab' => 4]);
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * метод позволяет менеджерам и руководителям 
     * перевести тип оплаты клиента в "остаточный". Для этого необходим ID оплаты.
     * @param int $id
     * 
     * @return mixed
     */

    public function actionRemain(int $id)
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        $model = $this->findModel($id);
        // проверяем что оплата действующая и не остаточная
        if ($model->visible==1 && $model->remain==0) {
            $model->remain = 1;
            if ($model->save(true, ['remain'])) {
                Yii::$app->session->setFlash('success', 'Оплата из "обычной" переведена в "остаточную".');
            }
            return $this->redirect(['studname/view', 'id' => $model->calc_studname, 'tab'=>4]);
        } else {
            return $this->redirect(Yii::$app->request->referrer); 
        }
    }

    /**
     * метод позволяет менеджерам и руководителям 
     * перевести тип оплаты клиента в "нормальный". Для этого необходим ID оплаты.
     * @param int $id
     * 
     * @return mixed
     */
    public function actionUnremain(int $id)
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        $model = $this->findModel($id);
        // проверяем что оплата действующая и остаточная
        if ($model->visible==1 && $model->remain==1) {
            $model->remain = 0;
            if ($model->save(true, ['remain'])) {
                Yii::$app->session->setFlash('success', 'Оплата из "остаточной" переведена в "обычную".');
            }
            return $this->redirect(['studname/view', 'id' => $model->calc_studname, 'tab'=>4]);
        } else {
            return $this->redirect(Yii::$app->request->referrer); 
        }
    }
    
    /**
     * @param integer $id
     * 
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        $payment = $this->findModel($id);
        /** @var Student $student */
        $student = $payment->student;
        if ($this->findModel($id)->delete()) {
            $student->updateInvMonDebt();
            Yii::$app->session->setFlash('success', 'Оплата успешно удалена.');
        }

        return $this->redirect(['studname/view', 'id' => $student->id, 'tab' => 4]);
    }

    /**
     * @return mixed
     */
	public function actionAutocomplete()
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4, 11])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        $students = Student::getStudentsAutocomplete(Yii::$app->request->post('term') ?? NULL);
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $students;
    }

    /**
     * Finds the Moneystud model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Moneystud the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Moneystud::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	/**
	 * Метод высчитывает долг клиента и возвращает значение
     * @param int $id
     * 
     * @return int
 	 */
    protected function studentDebt($id) : int
    {	
		// задаем переменную в которую будет подсчитан долг по занятиям
		$debt_lessons = 0;
		// задаем переменную в которую будет подсчитан долг по разнице между счетами и оплатами
		$debt_common = 0;
		// полный долг
		$debt = 0;
		
		// получаем информацию по счетам
		$invoices_sum = (new \yii\db\Query())
        ->select('sum(value) as money')
        ->from('calc_invoicestud')
		->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
        ->one();
		
		// получаем информацию по оплатам
		$payments_sum = (new \yii\db\Query())
        ->select('sum(value) as money')
        ->from('calc_moneystud')
		->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
        ->one();
		
		// считаем разницу как базовый долг
		$debt_common = $payments_sum['money'] - $invoices_sum['money'];
		
		// запрашиваем услуги назначенные студенту
		$services = (new \yii\db\Query())
		->select('s.id as sid, s.name as sname, SUM(is.num) as num')
		->distinct()
		->from('calc_service s')
		->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
		->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])
		->andWhere(['is.calc_studname'=>$id])
		->groupby(['is.calc_studname','s.id'])
		->orderby(['s.id'=>SORT_ASC])
		->all();
		
		// проверяем что у студента есть назначенные услуги
		if(!empty($services)){
			$i = 0;
			// распечатываем массив
			foreach($services as $service){
				// запрашиваем из базы колич пройденных уроков
				$lessons = (new \yii\db\Query())
				->select('COUNT(sjg.id) AS cnt')
				->from('calc_studjournalgroup sjg')
				->leftjoin('calc_groupteacher gt', 'sjg.calc_groupteacher=gt.id')
				->leftjoin('calc_journalgroup jg', 'sjg.calc_journalgroup=jg.id')
				->where('jg.view=:vis and jg.visible=:vis and (sjg.calc_statusjournal=:vis or sjg.calc_statusjournal=:stat) and gt.calc_service=:sid and sjg.calc_studname=:stid', [':vis'=>1, 'stat'=>3, ':sid'=>$service['sid'], ':stid'=>$id])
				->one();

				// считаем остаток уроков
				$services[$i]['num'] = $services[$i]['num'] - $lessons['cnt'];
				$i++;
			}
			
			foreach($services as $s) {
                if($s['num'] < 0){
						$lesson_cost = (new \yii\db\Query())
						->select('(value/num) as money')
						->from('calc_invoicestud')
						->where('visible=:vis and calc_studname=:stid and calc_service=:sid', [':vis'=>1, ':stid'=>$id, ':sid'=>$s['sid']])
						->orderby(['id'=>SORT_DESC])
						->one();
						
						$debt_lessons = $debt_lessons + $s['num'] * $lesson_cost['money'];
				}				
			}
		}
		$debt = $debt_common + $debt_lessons;
		//$debt = number_format($debt, 1, '.', ' ');
		return (int)$debt;
    }
    
    /**
     * @param string $value
     * 
     * @return float
     */
    protected function prepareInput(string $value) : float
    {
        $value = trim($value);
        $value = str_replace(',', '.', $value);

        return (float)$value;
    }
}
