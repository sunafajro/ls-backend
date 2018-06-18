<?php

namespace app\controllers;

use Yii;
use app\models\Moneystud;
use app\models\Student;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * MoneystudController implements the CRUD actions for Moneystud model.
 */
class MoneystudController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','delete','disable','enable', 'remain', 'unremain'],
                'rules' => [
                    [
                        'actions' => ['create','delete','disable','enable', 'remain', 'unremain'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','disable','enable','delete','remain','unremain'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Метод позволяет менеджерам и руководителям
     * создавать оплаты клиента. Для создания оплаты необходим ID клиента.
     */

    public function actionCreate()
    {
        // оплаты принимают только менеджеры или руководители
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            if(Yii::$app->request->get('sid')){
                $sid = Yii::$app->request->get('sid');                
            } else {
                // если нет возвращаемся на предыдущую страницу
                return $this->redirect(Yii::$app->request->referrer);                
            }
            $userInfoBlock = User::getUserInfoBlock();
            // создаем пустую модель записи об оплате
            $model = new Moneystud();

            // находим информацию по клиенту
            $student = Student::findOne($sid);
			
		    // получаем массив со списком офисов
			$tmp_offices = (new \yii\db\Query())
			->select('co.id as oid, co.name as oname')
			->from('calc_office co')
			->where('co.visible=:vis', [':vis'=>1])
			->orderBy(['co.id'=>SORT_ASC])
			->all();
			
			$offices = [];
			foreach($tmp_offices as $to) {
				$offices[$to['oid']] = $to['oname'];
			}
            unset($to);
			unset($tmp_offices);
            if ($model->load(Yii::$app->request->post())) {
                $model->value_cash = $this->prepareInput($model->value_cash);
                $model->value_card = $this->prepareInput($model->value_card);
                $model->value_bank = $this->prepareInput($model->value_bank);
                $model->value = $model->value_cash + $model->value_card + $model->value_bank;
                // указываем id клиента
                $model->calc_studname = $sid;
                $model->visible = 1;
                $model->user = Yii::$app->session->get('user.uid');
                $model->user_visible = 0;
                $model->user_remain = 0;
                $model->user_collection = 0;
                $model->collection = 0;
                $model->data = date('Y-m-d');
                $model->data_visible = '0000-00-00';
                $model->data_remain = '0000-00-00';
                $model->data_collection = '0000-00-00';
                // для менеджеров офис подставляем автоматом
                if(Yii::$app->session->get('user.ustatus')==4) {
                    $model->calc_office = Yii::$app->session->get('user.uoffice_id');
                }
                // если запись об оплате прошла успешно
                if($model->save()) {
                    // суммируем общее число оплат с новой оплатой
                    $student->money = $student->money + $model->value;
					// пересчитываем значение долга для старой системы
                    $student->debt = $student->money - $student->invoice;
					// пересчитываем баланс клиента новой функцией
					$student->debt2 = $this->studentDebt($student->id);
                    // сохраняем данные
                    $student->save();
                    // вызываем функцию рассчета долга
                    // $student->debt = $this->calcDebt($studdent->id);
                    // сохраняем данные
                    // $student->save();
                }
                return $this->redirect(['studname/view', 'id' => $student->id, 'tab'=>4]); 
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'student'=>$student,
					'offices'=>$offices,
                    'userInfoBlock' => $userInfoBlock
                ]);
            }
        } else {
            // если нет возвращаемся на предыдущую страницу
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
    * метод позволяет менеджерам и руководителям 
    * аннулировать оплату клиента. Для аннулирования необходим ID оплаты.
    **/

    public function actionDisable($id)
    {
        // оплаты могут аннулировать только менеджеры или руководители
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            // 
            $model = $this->findModel($id);
            // проверяем что оплата действующая
            if($model->visible==1) {
                // помечаем оплату как аннулированную
                $model->visible = 0;
                // указываем пользователя аннулировавшего оплату
                $model->user_visible = Yii::$app->session->get('user.uid');
                // указываем дату анулирования оплаты
                $model->data_visible = date('Y-m-d');
                // если запись успешно сохранилась
                if($model->save()) {
                    // находим клиента
                    $student = Student::findOne($model->calc_studname);
                    // вычитаем сумму оплаты из общего числа оплат
                    $student->money = $student->money - $model->value;
					// пересчитываем значение долга для старой системы
                    $student->debt = $student->money - $student->invoice;
					// пересчитываем баланс клиента новой функцией
					$student->debt2 = $this->studentDebt($student->id);
                    // сохраняем данные
                    $student->save();
                    // вызываем функцию рассчета долга
                    // $student->debt = $this->calcDebt($studdent->id);
                    // сохраняем данные
                    // $student->save();

                }
                return $this->redirect(['studname/view', 'id' => $student->id, 'tab'=>4]);
            } else {
                // возвращаемся обратно
                return $this->redirect(Yii::$app->request->referrer); 
            }
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
    * метод позволяет менеджерам и руководителям 
    * восстановить оплату клиента. Для восстановления необходим ID оплаты.
    **/
    
    public function actionEnable($id)
    {
        // оплаты могут восстановить только менеджеры или руководители
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            // 
            $model = $this->findModel($id);
            // проверяем что оплата аннулирована
            if($model->visible==0) {
                // помечаем оплату как действующую
                $model->visible = 1;
                // указываем пользователя восстановившего оплату
                $model->user_visible = Yii::$app->session->get('user.uid');
                // указываем дату восстановления оплаты
                $model->data_visible = date('Y-m-d');
                // если запись успешно сохранилась
                if($model->save()) {
                    // находим клиента
                    $student = Student::findOne($model->calc_studname);
                    // добавляем сумму оплаты к общему числу оплат
                    $student->money = $student->money + $model->value;
					// пересчитываем значение долга для старой системы
                    $student->debt = $student->money - $student->invoice;
					// пересчитываем баланс клиента новой функцией
					$student->debt2 = $this->studentDebt($student->id);
                    // сохраняем данные
                    $student->save();
                    // вызываем функцию рассчета долга
                    // $student->debt = $this->calcDebt($studdent->id);
                    // сохраняем данные
                    // $student->save();

                }
                return $this->redirect(['studname/view', 'id' => $student->id, 'tab'=>4]);
            } else {
                // возвращаемся обратно
                return $this->redirect(Yii::$app->request->referrer);
            }
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer);
        }

    }

    /**
    * метод позволяет менеджерам и руководителям 
    * перевести тип оплаты клиента в "остаточный". Для этого необходим ID оплаты.
    **/

    public function actionRemain($id)
    {
        // оплаты могут делать остаточными только менеджеры или руководители
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            // 
            $model = $this->findModel($id);
            // проверяем что оплата действующая и не остаточная
            if($model->visible==1 && $model->remain==0) {
                // помечаем оплату как остаточную
                $model->remain = 1;
                // сохраняем
                $model->save();
                return $this->redirect(['studname/view', 'id' => $model->calc_studname, 'tab'=>4]);
            } else {
                // возвращаемся обратно
                return $this->redirect(Yii::$app->request->referrer); 
            }
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
    * метод позволяет менеджерам и руководителям 
    * перевести тип оплаты клиента в "нормальный". Для этого необходим ID оплаты.
    **/

    public function actionUnremain($id)
    {
        // оплаты могут делать остаточными только менеджеры или руководители
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
            // 
            $model = $this->findModel($id);
            // проверяем что оплата действующая и остаточная
            if($model->visible==1 && $model->remain==1) {
                // помечаем оплату как обычную
                $model->remain = 0;
                // сохраняем
                $model->save();
                return $this->redirect(['studname/view', 'id' => $model->calc_studname, 'tab'=>4]);
            } else {
                // возвращаемся обратно
                return $this->redirect(Yii::$app->request->referrer); 
            }
        } else {
            // возвращаемся обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
    
    /**
     * Deletes an existing Moneystud model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if((int)Yii::$app->session->get('user.ustatus') === 3) {
            $payment = $this->findModel($id);
            $student = $payment->calc_studname;
            $this->findModel($id)->delete();
            return $this->redirect(['studname/view', 'id' => $student, 'tab'=>4]);
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
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

	/*
	* Метод высчитывает долг клиента и возвращает значение
 	*/
	
	protected function studentDebt($id) {
		
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
			// уничтожаем переменные
			unset($service);
			unset($lessons);
			
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
		unset($services);
		$debt = $debt_common + $debt_lessons;
		//$debt = number_format($debt, 1, '.', ' ');
		return (int)$debt;
    }
    
    protected function prepareInput($value) {
        $value = trim($value);
        $value = str_replace(',', '.', $value);
        return $value;
    }
}
