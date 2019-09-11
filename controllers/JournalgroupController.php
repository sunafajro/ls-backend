<?php

namespace app\controllers;

use Yii;
use app\models\Groupteacher;
use app\models\Journalgroup;
use app\models\Student;
use app\models\Tool;
use app\models\User;
//use app\models\StudJournalGroup;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
/**
 * JournalgroupController implements the CRUD actions for CalcJournalgroup model.
 */
class JournalgroupController extends Controller
{
    public function behaviors()
    {
        return [
	        'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','update','change','view', 'unview','delete','restore','remove', 'absent'],
                'rules' => [
                    [
                        'actions' => ['create','update','change','view', 'unview','delete','restore','remove', 'absent'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','update','change','view', 'unview','delete','restore','remove', 'absent'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
	
    /**
    * метод позволяет менеджеру,и руководителю
    * или преподавателю назначенному в группу
    * добавить занятие в журнал
    **/
	
    public function actionCreate($gid)
    {
        $group = Groupteacher::findOne($gid);
        $params['gid'] = $gid;
        $params['active'] = Groupteacher::getGroupStateById($gid);
        // получаем массив со списком преподавателей назначенных группе
        $check_teachers = Groupteacher::getGroupTeacherListSimple($gid);
        
        // проверяем роль пользователя, занятие в журнал могут добавить только преподаватели назначенные в группу, менеджеры или руководители
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           (int)Yii::$app->session->get('user.uid') === 296 ||
           array_key_exists(Yii::$app->session->get('user.uteacher'), $check_teachers) ||
           ((int)Yii::$app->session->get('user.ustatus') === 10 && $group->company === 2)) {
            // создаем новую пустую модель
            $model = new Journalgroup();
		
            // получаем список текущих студентов в группе
            $students = (new \yii\db\Query())
            ->select('s.id, s.name')
            ->from('calc_studgroup sg')
            ->leftJoin('calc_studname s', 's.id=sg.calc_studname')
            ->where('sg.calc_groupteacher=:gid and s.visible=1 and sg.visible=1', [':gid'=>$gid])
            ->orderby(['s.name'=>SORT_ASC])
            ->all();

            // если пришли данные и моделька загрузилась успешно, переходим в картоку преподавателя
            if ($model->load(Yii::$app->request->post())) {
                // если занятие добавляет преподаватель группы (не менеджер или руководитель)
                if((int)Yii::$app->session->get('user.ustatus') !== 3 &&
                   (int)Yii::$app->session->get('user.ustatus') !== 4 &&
                   (int)Yii::$app->session->get('user.uid') !== 296 &&
                   array_key_exists(Yii::$app->session->get('user.uteacher'), $check_teachers)) {
                    // выставляем учебное время как вечернее (2)
                    $model->calc_edutime = 2;
                }
                // если занятие добавляет не руководитель (и ольга воронецкая)
                if((int)Yii::$app->session->get('user.ustatus') !== 3 &&
                   (int)Yii::$app->session->get('user.uid') !== 62 &&
                   (int)Yii::$app->session->get('user.uid') !== 296 &&
                   (int)Yii::$app->session->get('user.ustatus') !== 10) {
                    // проверяем что со времени проведения занятия прошло не более 3 дней
                    $dt = new \DateTime('-5 days');
                    if($model->data <= $dt->format('Y-m-d')) {
                        // если более 3 дней, задаем сообщение об ошибке
                        Yii::$app->session->setFlash('error', 'Не удалось добавить занятие в журнал. С указанной даты прошло более 3 дней. Пожалуйста обратитесь к руководителю.');
                        // возвращаемся в журнал
                        return $this->redirect(['groupteacher/view', 'id' => $gid]);
                    }
                }
                // если в группу назначен только один преподаватель и в post-запросе его id явно не указан
                if(!$model->calc_teacher && count($check_teachers) == 1) {
                    // пишем id преподавателя из ранее полученного списка преподавателей
                    $keys = array_keys($check_teachers);
                    $model->calc_teacher = $keys[0];
                }
                // указываем id группы
                $model->calc_groupteacher = $gid;
                // помечаем занятие как действующее
                $model->visible = 1;
                $model->data_visible = '0000-00-00';
                $model->user_visible = 0;
                // указываем id пользователя добавившего занятие
                $model->user = Yii::$app->session->get('user.uid');
                /* параметры проверки занятия */
                $model->view = 0;
                $model->data_view = '0000-00-00';
                $model->user_done = 0;
                /* параметры оплаты занятия */
                $model->done = 0;
                $model->data_done = '0000-00-00';
                $model->user_view = 0;
                $model->calc_accrual = 0;
                /* параметры редактирования занятия */
                $model->edit = 0;
                $model->data_edit = '0000-00-00';
                $model->user_edit = 0;
                $model->audit = 0;
                $model->data_audit = '0000-00-00';
                $model->user_audit = 0;
                $model->description_audit = '';
                // если есть данные по посещению занятия студентами
                if (Yii::$app->request->post('CalcStudjournalgroup') && !empty(Yii::$app->request->post('CalcStudjournalgroup'))) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if(!$model->save()) {
                            throw new \Exception('Не удалось добавить занятие!');
                        }
                        // переприсваиваем массив из post в переменную
                        $arrs = Yii::$app->request->post('CalcStudjournalgroup');
                        // распечатываем массив и формируем новый многоуровневый
                        foreach ($arrs as $key => $value) {
                            if (substr($key, 0, 7) == 'comment') {
                                $arr[substr($key, 8)]['id'] = substr($key, 8);
                                $arr[substr($key, 8)]['comment'] = $value;
                            } else if (substr($key, 0, 6) == 'status') {
                                $arr[substr($key, 7)]['status'] = $value;
                            }
                        }
                        // распечатываем масcив с списком студентов
                        foreach ($arr as $s) {
                            // пишем данные о посещаемости в базу
                            $db = (new \yii\db\Query())
                                ->createCommand()
                                ->insert('calc_studjournalgroup', [
                                    'calc_groupteacher'=>$gid,
                                    'calc_journalgroup'=>$model->id,
                                    'calc_studname'=>$s['id'],
                                    'calc_statusjournal'=>$s['status'],
                                    'comments'=>$s['comment'],
                                    'data'=>$model->data,
                                    'user'=>$model->user,
                                ])
                                ->execute();
                        }
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Запись о занятии успешно добавлена в журнал.');
                        return $this->redirect(['groupteacher/view', 'id' => $gid]);
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Не удалось добавить занятие!');
                        return $this->redirect(['groupteacher/view', 'id' => $gid]);
                    }
                } else {
                    // если нет никакой информации по студентам на занятии, возвращаем ошибку
                    Yii::$app->session->setFlash('error', 'Не удалось добавить занятие!');
                    return $this->redirect(['groupteacher/view', 'id' => $gid]);
                }
            } else {
                // выводим форму добавления занятия
                return $this->render('create', [
                    'teachers'       => $check_teachers,
                    'groupInfo'      => Groupteacher::getGroupInfoById($gid),
                    'items'          => Groupteacher::getMenuItemList($gid, Yii::$app->controller->id . '/' . Yii::$app->controller->action->id),
                    'model'          => $model,
                    'params'         => $params,
                    'students'       => $students,
                    'userInfoBlock'  => User::getUserInfoBlock(),
                    
                ]);
            }
        }
        // если у пользователя другая роль
        else {
            // возвращаемся в журнал группы
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
    }
 
    /**
    * метод позволяет менеджерам, руководителям
    * и преподавателям назначенным в группу
    * редактировать запись о занятии в журнале
    **/
    public function actionUpdate($id, $gid)
    {
        $group = Groupteacher::findOne($gid);
        $params['gid'] = $gid;
        $params['active'] = Groupteacher::getGroupStateById($gid);
        // получаем массив со списком преподавателей назначенных группе
        $checkTeachers = Groupteacher::getGroupTeacherListSimple($gid);
        // находим запись о занятии
        $model = $this->findModel($id);        

        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           array_key_exists(Yii::$app->session->get('user.uteacher'), $checkTeachers) ||
           ((int)Yii::$app->session->get('user.ustatus') === 10 && $group->company === 2)) {
            // получаем данные из формы и обновляем запись
            if ($model->load(Yii::$app->request->post())) {
                // если занятие обновляет не руководитель (и не ольга воронецкая)
                if((int)Yii::$app->session->get('user.ustatus') !== 3 &&
                   (int)Yii::$app->session->get('user.uid') !== 62 &&
                   (int)Yii::$app->session->get('user.ustatus') !== 10 ) {
                    // проверяем что со времени проведения занятия прошло не более 3 дней
                    $dt = new \DateTime('-5 days');
                    if($model->data <= $dt->format('Y-m-d')) {
                        // если более 3 дней, задаем сообщение об ошибке
                        Yii::$app->session->setFlash('error', 'Не удалось обновить занятие в журнале. С указанной даты прошло более 3 дней. Пожалуйста обратитесь к руководителю.');
                        // возвращаемся в журнал
                        return $this->redirect(['groupteacher/view', 'id' => $gid]);
                    }
                }
                if($model->save()) {
                    // если модель сохранилась, задаем сообщение об успешном изменении занятия
                    Yii::$app->session->setFlash('success', 'Информация о занятии успешно обновлена!');
				} else {
                    // если модель не сохранилась, задаем сообщение об безуспешном изменении занятия
                    Yii::$app->session->setFlash('error', 'Не удалось обновить информацию о занятии!');
				}
                return $this->redirect(['groupteacher/view', 'id' => $gid]);
            }
			
            return $this->render('update', [
                'model'         => $model,
                'teachers'      => $checkTeachers,
                'groupInfo'     => Groupteacher::getGroupInfoById($gid),
                'items'         => Groupteacher::getMenuItemList($gid, Yii::$app->controller->id . '/' . Yii::$app->controller->action->id),
                'userInfoBlock' => User::getUserInfoBlock(),
                'params'        => $params,
            ]);
        } else {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
    }
	
    /** 
	*  метод позволяет менеджерам отредактировать состав студентов
	* посетивших или пропустивших занятие 
	**/
    public function actionChange($id, $gid)
	{
	    /* проверяем права доступа (! переделать в поведения !) */
	    if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
	        throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
	    }
	
	    // получаем список студентов занятия для редактирования состава 
	    $students = (new \yii\db\Query())
	    ->select('s.id as sid, s.name as sname, sjg.calc_statusjournal as status, sjg.comments as comment, sjg.data as ldate, u.name as user')
	    ->from('calc_journalgroup jg')
	    ->leftJoin('calc_studjournalgroup sjg', 'jg.id=sjg.calc_journalgroup')
	    ->leftJoin('calc_studname s', 's.id=sjg.calc_studname')
	    ->leftjoin('user u', 'sjg.user=u.id')
	    ->where('jg.calc_groupteacher=:gid and jg.id=:lid', [':gid'=>$gid, ':lid'=>$id])
	    ->orderby(['s.name'=>SORT_ASC])
	    ->all();

		// получаем историю статусов студентов
		$history = (new \yii\db\Query())
        ->select('s.id as sid, s.name as sname, sjgh.data as date, sj.name as stname, sjgh.timestamp_id as timestamp')
        ->from('calc_studjournalgrouphistory sjgh')
	    ->leftJoin('calc_studname s', 's.id=sjgh.calc_studname')
		->leftjoin('calc_statusjournal sj', 'sj.id=sjgh.calc_statusjournal')
		->where('sjgh.calc_journalgroup=:lid and sjgh.calc_groupteacher=:gid', [':lid'=>$id, ':gid'=>$gid])
        ->orderby(['sjgh.data'=>SORT_DESC, 's.name'=>SORT_ASC])
        ->all();

		$dates = [];
		if(!empty($history)) {
			foreach($history as $h) {
				$dates[$h['timestamp']] = $h['date'];
			}
			unset($h);
		}
		// проверяем массив на наличие нулевых записей
		$i = 0;
		//$j = 0;
		//$exist_in_array = [];
		//$count_of_elements = count($students);
		foreach($students as $s) {
			// если такие есть
			if($s['sid']==NULL && $s['sname']==NULL && $s['status']==NULL && $s['comment']==NULL && $s['ldate']==NULL && $s['user']==NULL){
				//удаляем
				unset($students[$i]);
			}// else {
			//	$exist_in_array[j] = $s['sid'];
			//	$j++;
			//}
		}
		unset($s);
		/*
		// проверяем что в исходном массиве не хватает статусов студентов 
		if($count_of_elements!=count($exist_in_array)){
			// если массив пустой то фильтр к запросу применяться не будет
			if(empty($exist_in_array)){
				$ss = NULL;
			}
			// находим студентов назначенных в группу
			$tmp_stds = (new \yii\db\Query())
			->select('s.id, s.name')
			->from('calc_studgroup sg')
			->leftJoin('calc_studname s', 's.id=sg.calc_studname')
			->where('sg.calc_groupteacher=:gid and s.visible=1 and sg.visible=1', [':gid'=>$gid])
			->andFilterWhere(['not in', 's.id', $ss])
			->orderby(['s.name'=>SORT_ASC])
			->all();
				
				if(!empty($tmp_stds)) {
				    foreach() {
					
				    }
				}
			}
		}
		*/
	    if(Yii::$app->request->post()){
			
			// если есть данные по посещению занятия студентами
			if(Yii::$app->request->post('Studjournalgroup') && !empty(Yii::$app->request->post('Studjournalgroup'))){
				// переприсваиваем массив из post в переменную
				$arrs = Yii::$app->request->post('Studjournalgroup');
				// распечатываем массив и формируем новый многоуровневый
				foreach($arrs as $key => $value){
					if(substr($key,0,7)=='comment'){
						$arr[substr($key,8)]['id'] = substr($key,8);
						$arr[substr($key,8)]['comment'] = $value;
					}
					elseif(substr($key,0,6)=='status'){
						$arr[substr($key,7)]['status'] = $value;
					}
				}
				unset($arrs);
				// получаем старые статусы
				$oldstatuses = (new \yii\db\Query())
				->select('*')
				->from('calc_studjournalgroup')
				->where('calc_journalgroup=:lid',[':lid'=>$id])
				->all();
				
				// генерим таймстемп который потом понадобиться при переносе занятий
			    $timestamp = time();
				//переносим старые записи из одной таблицы в другую				
				foreach($oldstatuses as $os) {
					// пишем данные о посещаемости в базу
					$db = (new \yii\db\Query())
					->createCommand()
					->insert('calc_studjournalgrouphistory', [
					'calc_groupteacher'=>$os['calc_groupteacher'],
					'calc_journalgroup'=>$os['calc_journalgroup'],
					'calc_studname'=>$os['calc_studname'],
					'calc_statusjournal'=>$os['calc_statusjournal'],
					'comments'=>$os['comments'],
					'data'=>$os['data'],
					'user'=>$os['user'],
					'timestamp_id'=>$timestamp,
					])
					->execute();
					unset($db);
					// удаляем записи о посещении занятия студентами
	                $db = (new \yii\db\Query())
					->createCommand()
					->delete('calc_studjournalgroup', 'id=:id')
					->bindParam(':id',$os['id'])
					->execute();
					unset($db);
				}
				unset($os);
				unset($oldstatuses);
				
				// распечатываем массив с списком 
				foreach($arr as $s){
					// пишем данные о посещаемости в базу
					$db = (new \yii\db\Query())
					->createCommand()
					->insert('calc_studjournalgroup', [
					'calc_groupteacher'=>$gid,
					'calc_journalgroup'=>$id,
					'calc_studname'=>$s['id'],
					'calc_statusjournal'=>$s['status'],
					'comments'=>$s['comment'],
					'data'=>date('Y-m-d'),
					'user'=>Yii::$app->session->get('user.uid'),
					])
					->execute();
					unset($db);
					// апдейтим баланс клиента
					if($s['status']==1||$s['status']==3) {
						// находим информацию по клиенту
						$student = Student::findOne($s['id']);
						// пересчитываем баланс клиента новой функцией
						$student->debt2 = $this->studentDebt($student->id);
						// сохраняем данные
						$student->save();
						unset($student);
					}
				}
			}			
			// если модель сохранилась, задаем сообщение об успешном изменении занятия
            Yii::$app->session->setFlash('success', 'Информация о составе занятия успешно обновлена.');
			return $this->redirect(['groupteacher/view', 'id' => $gid]);	        
	    } else {
	        return $this->render('change', [
                'checkTeachers' => Groupteacher::getGroupTeacherListSimple($gid),
                'dates'         => $dates,
                'groupInfo'     => Groupteacher::getGroupInfoById($gid),
                'history'       => $history,
                'items'         => Groupteacher::getMenuItemList($gid, Yii::$app->controller->id . '/' . Yii::$app->controller->action->id),
                'params'        => [
                    'active'    => Groupteacher::getGroupStateById($gid),
                    'gid'       => (int)$gid,
                    'lid'       => (int)$id,
                ],
				'students'      => $students,
                'userInfoBlock' => User::getUserInfoBlock(),
    		]);
		}
    }
	
	/**
    * метод позволяет менеджеру или руководителю отметить
    * занятие как проверенное
    **/
    public function actionView($gid, $id)
	{
        /* проверяем права доступа (! переделать в поведения !) */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 &&
           (int)Yii::$app->session->get('user.ustatus') !== 4 &&
           (int)Yii::$app->session->get('user.uid') !== 296) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
		
        // находим запись по id
        $model = $this->findModel($id);
		
		/* проверяем что со времени проведения занятия прошло не более 3 дней
		$dt = new \DateTime('-3 days');
		if($model->data <= $dt->format('Y-m-d') && Yii::$app->session->get('user.ustatus')==4){
			// если более 3 дней, задаем сообщение об ошибке
			Yii::$app->session->setFlash('error', 'Не удалось проверить занятие. С указанной даты прошло более 3 дней. Пожалуйста обратитесь к руководителю.');
			// возвращаемся в журнал
			return $this->redirect(['groupteacher/view', 'id' => $gid]);
		}
		*/
		// получаем список id студентов
		$query = (new \yii\db\Query())
		->select('sjg.calc_studname as sid, sn.name as name, gt.calc_service as service')
		->from('calc_journalgroup jg')
		->leftjoin('calc_studjournalgroup sjg', 'sjg.calc_journalgroup=jg.id')
		->leftjoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
		->leftjoin('calc_studname sn' , 'sn.id=sjg.calc_studname')
		->where('jg.id=:jid and jg.calc_groupteacher=:gid', [':jid'=>$id, ':gid'=>$gid])
		->andWhere(['in', 'sjg.calc_statusjournal', [1]])
		->all();

		$i = 0;
		$snames = '';
		$sids = [];
		foreach($query as $q) {
			// запрашиваем услуги назначенные студенту
			$services = (new \yii\db\Query())
			->select('s.id as sid, s.name as sname, SUM(is.num) as num')
			->distinct()
			->from('calc_service s')
			->leftjoin('calc_invoicestud is', 'is.calc_service=s.id')
			->where('is.remain=:rem and is.visible=:vis', [':rem'=>0, ':vis'=>1])
			->andWhere(['is.calc_studname'=>$q['sid']])
			->andWhere(['s.id'=>$q['service']])
			->groupby(['is.calc_studname','s.id'])
			->orderby(['s.id'=>SORT_ASC])
			->one();
            
			// проверяем что у студента есть назначенные услуги
			if(!empty($services)){
				// запрашиваем из базы колич пройденных уроков
				$lessons = (new \yii\db\Query())
				->select('COUNT(sjg.id) AS cnt')
				->from('calc_studjournalgroup sjg')
				->leftjoin('calc_groupteacher gt', 'sjg.calc_groupteacher=gt.id')
				->leftjoin('calc_journalgroup jg', 'sjg.calc_journalgroup=jg.id')
				->where('jg.view=:vis and jg.visible=:vis and (sjg.calc_statusjournal=:vis or sjg.calc_statusjournal=:stat) and gt.calc_service=:sid and sjg.calc_studname=:stid', [':vis'=>1, 'stat'=>3, ':sid'=>$q['service'], ':stid'=>$q['sid']])
				->one();

				// считаем остаток уроков
				$services['num'] = $services['num'] - $lessons['cnt'];
				unset($lessons);
			}
			if($services['num'] <= 0){
				$snames .= $q['name'].', ';
			}
			$sids[$i] = $q['sid'];
			$i++;
		}
		unset($query);
		// проверяем что занятие действительно не проверено и нет студентов должников
		if($model->view != 1 && $snames == '') {
			// меняем параметр проверки на 1
			$model->view = 1;
			// указываем пользователя проверившего занятия
			$model->user_view = Yii::$app->session->get('user.uid');
			// указывае дату проверки
			$model->data_view = date('Y-m-d');
			// если должников нет, и модель сохранилась
			if($model->save()) {
				// добавлем сообщение об успешной проверке занятия
				Yii::$app->session->setFlash('success', 'Занятие успешно переведено в проверенные.');
			}
		} else {
			$snames = mb_substr($snames, 0, -2);
			$snames .= '.';
			// если должники есть, задаем сообщение об невозможности проверки занятия
			Yii::$app->session->setFlash('error', 'Невозможно проверить занятие. Выставите счета студентам: '.$snames);
		}
		// обновляем балансы клиентов
		foreach($sids as $key => $value) {
			// находим информацию по клиенту
			$student = Student::findOne($value);
			// пересчитываем баланс клиента новой функцией
			$student->debt2 = $this->studentDebt($student->id);
			// сохраняем данные
			$student->save();
			unset($student);
	    }
		unset($key);
		unset($value);
		unset($sids);
        // возвращаемся на страницу добавления студентов в группу
        return $this->redirect(['groupteacher/view', 'id'=>$gid]);
    }
	
	/**
    * метод позволяет менеджеру или руководителю
	* снять с занятия отметку о проверке
    **/
    public function actionUnview($gid, $id)
	{
        /* проверяем права доступа (! переделать в поведения !) */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 &&
           (int)Yii::$app->session->get('user.ustatus') !== 4 &&
           (int)Yii::$app->session->get('user.uid') !== 296) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
		
		// находим запись по id
		$model = $this->findModel($id);
		// проверяем что занятие действительно проверено
        if($model->view == 1) {
            // меняем параметр проверки на 0
    	    $model->view = 0;
    	    // указываем пользователя отменившего отметку о проверке занятия
    	    $model->user_view = Yii::$app->session->get('user.uid');
    	    // указывае дату отмены отметки о проверке
    	    $model->data_view = date('Y-m-d');
    	    // сохраняем запись и проверяем успешность
		    if($model->save()) {
				$sids = (new \yii\db\Query())
				->select('sjg.calc_studname as id')
				->from('calc_studjournalgroup sjg')
				->where('sjg.calc_journalgroup=:jid', [':jid'=>$id])
				->all();
				// обновляем балансы клиентов
				foreach($sids as $s) {
					// находим информацию по клиенту
					$student = Student::findOne($s['id']);
					// пересчитываем баланс клиента новой функцией
					$student->debt2 = $this->studentDebt($student->id);
					// сохраняем данные
					$student->save();
					unset($student);
				}
				unset($s);
				unset($sids);
				// добавлем сообщение об успешной отмене проверки занятия
                Yii::$app->session->setFlash('success', 'Занятие успешно возвращено в непроверенные.');
			}
		}
        // возвращаемся на страницу добавления студентов в группу
        return $this->redirect(['groupteacher/view', 'id'=>$gid]);
    }
	
    /**
    * метод позволяет преподавателю назначенному в группу,
    * менеджеру или руководителю,
    * исключить запись о занятии из журнала
    **/
    public function actionDelete($gid, $id)
    {
        $group = Groupteacher::findOne($gid);
        // находим запись по id
        $model = $this->findModel($id);

        // получаем массив со списком преподавателей назначенных группе
        $teachers = (new \yii\db\Query())
        ->select('tg.calc_teacher as id, t.name as name')
        ->from('calc_teachergroup tg')
        ->leftjoin('calc_teacher t', 't.id=tg.calc_teacher')
        ->where('tg.calc_groupteacher=:gid and t.old=:zero and t.visible=:vis and tg.visible=:vis', [':gid'=>$gid, ':vis'=>1, ':zero'=>0])
        ->orderby(['t.name'=>SORT_ASC])
        ->all();

        // задаем переменную для ключей массива
        $i = 0;
        // готовим массив со списком преподавателем для проверки права доступа
        foreach($teachers as $tt) {
            // заполняем массив
            $tmp_teachers[$i] = $tt['id'];
            // увеличиваем ключ
            $i++;
        }
        unset($teachers);

        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           in_array(Yii::$app->session->get('user.uteacher'), $tmp_teachers) ||
           ((int)Yii::$app->session->get('user.ustatus') === 10 && $group->company === 2)) {
            // меняем параметр видимости на 0
            $model->visible = 0;
            // указываем пользователя исключившего занятие
            $model->user_visible = Yii::$app->session->get('user.uid');
            // указывае дату исключения занятия
            $model->data_visible = date('Y-m-d');
            // сохраняем запись
            $model->save();
            // если модель сохранилась, задаем сообщение об успешном изменении занятия
            Yii::$app->session->setFlash('success', 'Занятие успешно исключено из журнала.');
			// получаем список студентов занятия
			$tmp_students = (new \yii\db\Query())
			->select('sjg.calc_studname as id, sjg.calc_statusjournal as status')
			->from('calc_studjournalgroup sjg')
			->where('sjg.calc_journalgroup=:sjid', [':sjid'=>$model->id])
			->all();
			//var_dump($tmp_students);die();
			foreach($tmp_students as $s) {
				// апдейтим баланс клиента
				if($s['status']==1||$s['status']==3) {
					// находим информацию по клиенту
					$student = Student::findOne($s['id']);
					// пересчитываем баланс клиента новой функцией
					$student->debt2 = $this->studentDebt($student->id);
					// сохраняем данные
					$student->save();
					unset($student);
				}
			}
			unset($s);
			unset($tmp_students);
            // возвращаемся на страницу добавления студентов в группу
            return $this->redirect(['groupteacher/view', 'id'=>$gid]);
        } else {
            /* уведомляем об отсуствии прав */
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
    }
	
    /**
    * метод позволяет менеджерам или руководителям
    * восстановить занятие, которое 
    * ранее было исключено из журнала
    **/
	
    public function actionRestore($gid, $id)
    {
        $group = Groupteacher::findOne($gid);	
        // находим запись по id
        $model = $this->findModel($id);

        // получаем массив со списком преподавателей назначенных группе
        $teachers = (new \yii\db\Query())
        ->select('tg.calc_teacher as id, t.name as name')
        ->from('calc_teachergroup tg')
        ->leftjoin('calc_teacher t', 't.id=tg.calc_teacher')
        ->where('tg.calc_groupteacher=:gid and t.old=:zero and t.visible=:vis and tg.visible=:vis', [':gid'=>$gid, ':vis'=>1, ':zero'=>0])
        ->orderby(['t.name'=>SORT_ASC])
        ->all();

        // задаем переменную для ключей массива
        $i = 0;
        // готовим массив со списком преподавателем для проверки права доступа
        foreach($teachers as $tt) {
            // заполняем массив
            $tmp_teachers[$i] = $tt['id'];
            // увеличиваем ключ
            $i++;
        }
        unset($teachers);

        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           in_array(Yii::$app->session->get('user.uteacher'), $tmp_teachers) ||
           ((int)Yii::$app->session->get('user.ustatus') === 10 && $group->company === 2)){
            // меняем параметр видимости на 1
            $model->visible = 1;
            // указываем пользователя восстановившего занятие
            $model->user_visible = Yii::$app->session->get('user.uid');
            // указывае дату восстановления занятия
            $model->data_visible = date('Y-m-d');
            // сохраняем запись
            $model->save();
            // если модель сохранилась, задаем сообщение об успешном изменении занятия
            Yii::$app->session->setFlash('success', 'Занятие успешно восстановлено в журнал.');
			// получаем список студентов занятия
			$tmp_students = (new \yii\db\Query())
			->select('sjg.calc_studname as id, sjg.calc_statusjournal as status')
			->from('calc_studjournalgroup sjg')
			->where('sjg.calc_journalgroup=:sjid', [':sjid'=>$id])
			->all();
			foreach($tmp_students as $s) {
				// апдейтим баланс клиента
				if($s['status']==1||$s['status']==3) {
					// находим информацию по клиенту
					$student = Student::findOne($s['id']);
					// пересчитываем баланс клиента новой функцией
					$student->debt2 = $this->studentDebt($student->id);
					// сохраняем данные
					$student->save();
					unset($student);
				}
			}
			unset($s);
			unset($tmp_students);
            // возвращаемся на страницу добавления студентов в группу
            return $this->redirect(['groupteacher/view', 'id'=>$gid]);
        } else {
            /* уведомляем об отсуствии прав */
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
    }
	
	/**
    * метод позволяет руководителям 
	* физически удалить запись о занятии из базы
    **/
    public function actionRemove($gid, $id)
	{
        /* проверяем права доступа (! переделать в поведения !) */
        if((int)Yii::$app->session->get('user.ustatus') !== 3) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        // удаляем запись
        $db = (new \yii\db\Query())->createCommand()->delete('calc_journalgroup', 'id=:lid')->bindParam(':lid',$lid)->execute();
        // удаляем записи о посещении занятия студентами
        $db = (new \yii\db\Query())->createCommand()->delete('calc_studjournalgroup', 'calc_journalgroup=:lid')->bindParam(':lid',$lid)->execute();
        // удаляем записи опосещении занятия студентами из истории
        $db = (new \yii\db\Query())->createCommand()->delete('calc_studjournalgrouphistory', 'calc_journalgroup=:lid')->bindParam(':lid',$lid)->execute();
        // возвращаемся на страницу журнала
        return $this->redirect(['groupteacher/view', 'id'=>$gid]);		
    }

    /** 
     *  метод позволяет менеджерам и руководителям
     *  изменить посещение урока учеником с "Не было"
     *  на "Не было (справка)"
     */
    public function actionAbsent($jid, $sid, $gid)
    {
        /* проверяем права доступа (! переделать в поведения !) */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        /* получаем информацию по присутствию на занятии */ 
        $data = (new \yii\db\Query())
        ->select('*')
        ->from('calc_studjournalgroup')
        ->where('calc_journalgroup=:jid AND calc_studname=:sid', [':jid'=>$jid, ':sid'=>$sid])
        ->one(); 

        if ($data) {
            // генерим таймстемп который потом понадобиться при переносе занятий
            $timestamp = time();
            /* переносим старые записи из одной таблицы в другую */
            /* пишем данные о посещаемости в базу */
            $db = (new \yii\db\Query())
            ->createCommand()
            ->insert('calc_studjournalgrouphistory', [
            'calc_groupteacher'=>$data['calc_groupteacher'],
            'calc_journalgroup'=>$data['calc_journalgroup'],
            'calc_studname'=>$data['calc_studname'],
            'calc_statusjournal'=>$data['calc_statusjournal'],
            'comments'=>$data['comments'],
            'data'=>$data['data'],
            'user'=>$data['user'],
            'timestamp_id'=>$timestamp,
            ])
            ->execute();
            unset($db);

            // удаляем записи о посещении занятия студентами
            $db = (new \yii\db\Query())
            ->createCommand()
            ->delete('calc_studjournalgroup', 'id=:id')
            ->bindParam(':id',$data['id'])
            ->execute();

            /* пишем данные о посещаемости в базу */
            $db = (new \yii\db\Query())
            ->createCommand()
            ->insert('calc_studjournalgroup', [
            'calc_groupteacher'=>$data['calc_groupteacher'],
            'calc_journalgroup'=>$data['calc_journalgroup'],
            'calc_studname'=>$data['calc_studname'],
            'calc_statusjournal'=> 2,
            'comments'=>$data['comments'],
            'data'=>date('Y-m-d'),
            'user'=>Yii::$app->session->get('user.uid'),
            ])
            ->execute();
            unset($db);
           
            /* если модель сохранилась, задаем сообщение об успешном изменении занятия */
            Yii::$app->session->setFlash('success', 'Информация о посещении занятия учеником успешно обновлена.');
        } else {
            /* если модель сохранилась, задаем сообщение об успешном изменении занятия */
            Yii::$app->session->setFlash('error', 'Не удалось найти информацию о посещении занятия.');
        }

        return $this->redirect(['groupteacher/view', 'id' => $gid]);
    }

    /**
     * Finds the CalcJournalgroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcJournalgroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Journalgroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Метод позволяет менеджерам и руководителям
     * создавать оплаты клиента. Для создания оплаты необходим ID клиента.
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
}
