<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use app\models\Ticket;
use app\models\Ticketreport;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
/**
 * JournalgroupController implements the CRUD actions for CalcTicket model.
 */
class TicketController extends Controller
{
	public function behaviors()
    {
        return [
	        'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'addexecutor', 'delexecutor', 'publish', 'accept', 'adjourn', 'disable', 'resume'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'addexecutor', 'delexecutor', 'publish', 'accept', 'adjourn', 'disable', 'resume'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'addexecutor', 'delexecutor', 'publish', 'accept', 'adjourn', 'disable', 'resume'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
			],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'accept' => ['post'],
                ],
            ],
        ];
    }
	
    /**
     * Lists all CalcTicket models.
     * @return mixed
     */
    public function actionIndex()
    {
	    $this->layout = 'column2';
		if(!Yii::$app->request->get('type') || Yii::$app->request->get('type') == 1) {
			$type = 1;
		} else {
			$type = 2;
		}
		if($type == 1) {
			$model = (new \yii\db\Query())
			->select('t.id as tid, t.user as creator_id, u1.name as creator, t.executor as executor, t.title as title, t.body as body, t.visible as visible, t.deadline as deadline, ts.id as status_id, ts.name as status, ts.color as color, t.closed as closed, t.comment as comment')
			->from('calc_ticket t')
			->leftJoin('user u1', 'u1.id=t.user')
			->leftJoin('calc_ticketstatus ts', 'ts.id=t.calc_ticketstatus')
			->where('t.visible=:vis and t.user=:id', [':vis'=>1,':id'=>Yii::$app->session->get('user.uid')])
			->orderby(['t.data'=> SORT_DESC, 't.id'=>SORT_DESC])
			->all();
		} else {
			$model = (new \yii\db\Query())
			->select('t.id as tid, t.user as creator_id, u1.name as creator, t.executor as executor, t.title as title, t.body as body, t.visible as visible, tr.data as deadline, ts.id as status_id, ts.name as status, ts.color as color, t.closed as closed, tr.comment as comment')
			->from('calc_ticket t')
			->leftJoin('calc_ticket_report tr' , 'tr.calc_ticket=t.id')
			->leftJoin('user u1', 'u1.id=t.user')
			->leftJoin('calc_ticketstatus ts', 'ts.id=tr.calc_ticketstatus')
			->where('t.visible=:vis and tr.user=:id and tr.calc_ticketstatus!=:st', [':vis'=>1,':id'=>Yii::$app->session->get('user.uid'), ':st'=>6])
			->orderby(['t.data'=> SORT_DESC, 't.id'=>SORT_DESC])
			->all();
		}
		
		$e_ids = [];
		$e_nms = [];
		
                $executors = NULL;
		if(!empty($model)) {
			$tids = NULL;
			foreach($model as $t) {
				$tids[] = $t['tid'];
			}
			unset($t);

                $executors = (new \yii\db\Query())
                ->select('tr.calc_ticket as tid, u.id as uid, u.name as uname, tr.data as deadline, ts.color as color')
                ->from('calc_ticket_report tr')
                ->leftJoin('user u', 'u.id=tr.user')
                ->leftJoin('calc_ticketstatus ts', 'ts.id=tr.calc_ticketstatus')
                ->andFilterWhere(['in', 'tr.calc_ticket', $tids])
                ->all(); 

			if(!empty($executors)) {
				foreach($tids as $t) {
					foreach($executors as $e) {
						if($e['tid']==$t) {
							$e_ids[$t][] = $e['uid'];
					        $e_nms[$t][$e['uid']] = $e['uname'];
						}
					}
					unset($e);
				}
			}
        }
        return $this->render('index', [
            'model' => $model,
			'e_ids' => $e_ids,
			'e_nms' => $e_nms,
			'type' => $type,
                        'executors' => $executors,
        ]);
    }
	
    /**
     * Creates a new CalcJournalgroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$this->layout = 'column2';
        $model = new Ticket();

        if ($model->load(Yii::$app->request->post())) {
			$model->user = Yii::$app->session->get('user.uid');
			$model->data = date('Y-m-d');
			$model->visible = 1;
			$model->calc_ticketstatus = 6;
            $model->edit = 0;
			$model->closed = 0;
            $model->published = 0;
			$model->executor = 0;
			$model->save();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                //'emps'=>$emps,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $this->layout = 'column2';
        $model = $this->findModel($id);

        $employee = (new \yii\db\Query())
        ->select('u.id as uid, u.name as uname')
        ->from('user u')
        ->where('u.visible=:vis', [':vis'=>1])
        ->orderBy(['u.name'=>SORT_ASC])
        ->all();

        foreach($employee as $e){
            $emps[$e['uid']] = $e['uname'];
        }
        unset($e);
        unset($employee);

		$ids = [1,2,3,4];
		
		$statuses = (new \yii\db\Query())
		->select('id as id, name as name')
		->from('calc_ticketstatus')
		->where('visible=:vis', [':vis' => 1])
		->andWhere(['in', 'id', $ids])
		->all();
		
		$status = [];
		foreach($statuses as $st){
			$status[$st['id']] = $st['name'];
		}
		
        if($model->user==\Yii::$app->session->get('user.uid')){
            if ($model->load(Yii::$app->request->post())) {
                $model->user_edit = Yii::$app->session->get('user.uid');
                $model->data_edit = date('Y-m-d');
                $model->edit = 1;
                $model->save();
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'emps'=>$emps,
					'status'=>$status,
                ]);
            }
        }
        return $this->redirect(['index']);
    }

	/** 
	 * метод публикует задачу, и она становится видна исполнителям.
	 * требует id задачи.
	 */
	public function actionPublish($id)
	{
		$model = $this->findModel($id);
		if($model->user==\Yii::$app->session->get('user.uid')){
			$model->calc_ticketstatus = 5;
			if($model->save()) {
				$data = (new \yii\db\Query())
				->createCommand()
				->update('calc_ticket_report', ['calc_ticketstatus'=>5], ['calc_ticket'=>$id])
				->execute();
			}
		}
		return $this->redirect(['index']);		
	}
	
	public function actionResume($id)
	{
		$ticket = $this->findModel($id);
		$model = Ticketreport::find()->where('calc_ticket=:t and user=:u', [':t' => $id, ':u' => \Yii::$app->session->get('user.uid')])->one();
		if($model->user==\Yii::$app->session->get('user.uid')){
			$model->calc_ticketstatus = 4;
			if($model->save()) {
				if($ticket->calc_ticketstatus==1 || $ticket->calc_ticketstatus==3) {
					$ticket->calc_ticketstatus = 4;
					$ticket->save();
				}
			}
		}
		return $this->redirect(['index', 'type'=>2]);		
	}

	public function actionClose($id)
	{
		$this->layout = 'column2';
		$ticket = $this->findModel($id);
		$model = Ticketreport::find()->where('calc_ticket=:t and user=:u', [':t' => $id, ':u' => \Yii::$app->session->get('user.uid')])->one();
		
		$ids = [1,3];
		
		$statuses = (new \yii\db\Query())
		->select('id as id, name as name')
		->from('calc_ticketstatus')
		->where('visible=:vis', [':vis' => 1])
		->andWhere(['in', 'id', $ids])
		->all();
		
		$status = [];
		foreach($statuses as $st){
			$status[$st['id']] = $st['name'];
		}
		
		$state = 1;
		
	    if ($model->load(\Yii::$app->request->post())){
			if($model->save()){
				$states = [2, 4];
				$check = Ticketreport::find()->where('calc_ticket=:t', [':t' => $id])->andWhere(['in', 'calc_ticketstatus', $states])->one();
				if($check === NULL) {
					$ticket->calc_ticketstatus = 1;
		            $ticket->data_closed = date('Y-m-d');
			        $ticket->closed = 1;
					$ticket->save();
				}
			}
			
			return $this->redirect(['index', 'type' => 2]);
	    }
		
		return $this->render('close', [
		    'model' => $model,
		    'status' => $status,
			'state' => $state,
			]);
	}
	
	/* Метод позволяет пользователю принять задачу к исполнению */
	public function actionAccept()
	{
		$id = Yii::$app->request->post('id');
		if ($id) {
			/* включаем формат ответа JSON */
			Yii::$app->response->format = Response::FORMAT_JSON;
			/* находим задачу */
			if (($ticket = Ticket::findOne($id)) !== NULL) {
				$model = Ticketreport::find()->where('calc_ticket=:t and user=:u', [':t' => $id, ':u' => (int)Yii::$app->session->get('user.uid')])->one();
				if ($model !== NULL) {
					$model->calc_ticketstatus = 4;
					if ($model->save()) {
						if ((int)$ticket->calc_ticketstatus === 5) {
							$ticket->calc_ticketstatus = 4;
							$ticket->save();
						}
						return [ 'result' => true ];
					} else {
						return [
							'result' => false,
						    'errMessage' => 'Не удалось принять задачу №' . $id
						];
					}
				} else {
					return [
						'result' => false,
					    'errMessage' => 'Исполнитель задачи №' . $id . ' не найден.'
					];
				}
			} else {
				return [
					'result' => false,
					'errMessage' => 'Задача №' . $id . ' не найдена.'
				];
			}
	    } else {
			return [
				'result' => false,
			    'errMessage' => 'Идентификатор задачи не задан.'
			];
		}
	}

	public function actionAdjourn($id)
	{
		$this->layout = 'column2';
		$ticket = $this->findModel($id);
		$model = Ticketreport::find()->where('calc_ticket=:t and user=:u', [':t' => $id, ':u' => \Yii::$app->session->get('user.uid')])->one();
		
		$state = 2;
		
	    if ($model->load(\Yii::$app->request->post())){
			$model->calc_ticketstatus = 2;
			$model->save();
			return $this->redirect(['index', 'type' => 2]);
	    }
		
		return $this->render('close', [
		    'model' => $model,
		    'state' => $state,
			]);
	}

    public function actionDisable($id)
    {
        $model = $this->findModel($id);
        if($model->user==\Yii::$app->session->get('user.uid') && $model->visible==1){
            $model->visible = 0;
            $model->user_visible = \Yii::$app->session->get('user.uid');
            $model->data_visible = date('Y-m-d');
            $model->save();
        }
        return $this->redirect(['index']);      
    }
	
    public function actionAddexecutor($id)
    {
		$this->layout = 'column2';
		$ids = NULL;
		$ticket = $this->findModel($id);
		$current_users = (new \yii\db\Query())
		->select('tr.id, u.name as name, ts.name as state, ts.color as color, tr.comment as comment')
		->from('calc_ticket_report tr')
		->leftJoin('user u', 'u.id=tr.user')
		->leftJoin('calc_ticketstatus ts', 'ts.id=tr.calc_ticketstatus')
		->where('tr.calc_ticket=:tid', [':tid'=>$id])
		->all();
		
		if($current_users && !empty($current_users)) {
			foreach($current_users as $cu) {
				$ids[] = $cu['id']; 
			}
			unset($cu);
		}
		
		// формируем список доступных исполнителей
		$employee = (new \yii\db\Query())
        ->select('u.id as uid, u.name as uname')
        ->from('user u')
        ->where('u.visible=:vis', [':vis'=>1])
		->andFilterWhere(['not in', 'u.id', $ids])
        ->orderBy(['u.name'=>SORT_ASC])
        ->all();
		
        foreach($employee as $e){
            $emps[$e['uid']] = $e['uname'];
        }
        unset($e);
        unset($employee);
		// формируем список доступных исполнителей
		
		$model = new Ticketreport();
		
		if($model->load(\Yii::$app->request->post())) {
			$model->calc_ticket = $id;
			$model->data = $ticket->deadline;
			if($ticket->calc_ticketstatus != 6) {
				$model->calc_ticketstatus = 5;
			} else {
			    $model->calc_ticketstatus = 6;
			}
			if($model->save()) {
				$ticket->executor = $ticket->executor + 1;
				$ticket->save();
			}
			return $this->redirect(['addexecutor', 'id' => $ticket->id]);
        }
		
        return $this->render('addexecutor', [
			'model' => $model,
			'emps' => $emps,
			'ticket' => $ticket,
			'current_users' => $current_users,
		]);
	}
	
    public function actionDelexecutor($id)
    {
		$model = Ticketreport::findOne($id);
		if($model !== NULL) {
			$ticket = Ticket::findOne($model->calc_ticket);
			if($ticket !== NULL) {
				if($model->delete()) {
					$ticket->executor = $ticket->executor - 1;
					$ticket->save();
				}
				return $this->redirect(['addexecutor', 'id' => $ticket->id]);
			}
		}
		return $this->redirect(['index']);
	}
	/**
     * Finds the CalcTicket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcTicket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
