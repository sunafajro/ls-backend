<?php

namespace app\modules\school\controllers;

use Yii;
use app\models\Groupteacher;
use app\models\Journalgroup;
use app\models\Student;
use app\models\Studjournalgroup;
use app\modules\school\models\User;
use yii\base\Exception;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
/**
 * JournalgroupController implements the CRUD actions for Journalgroup model.
 */
class JournalgroupController extends Controller
{
    public function behaviors()
    {
        return [
	        'access' => [
                'class' => AccessControl::class,
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'remove' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Метод добавления занятия в журнал группы
     * @param int $gid
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCreate($gid)
    {
        $roleId    = (int)Yii::$app->session->get('user.ustatus');
        $userId    = (int)Yii::$app->session->get('user.uid');
        $teacherId = (int)Yii::$app->session->get('user.uteacher');
        /** @var Groupteacher group */
        $group = Groupteacher::findOne($gid);
        if (empty($group)) {
            throw new NotFoundHttpException("Группа №{$gid} не найдена.");
        }
        $params['gid'] = $gid;
        $params['active'] = $group->visible ?? null;
        // получаем массив со списком преподавателей назначенных группе
        $groupTeachers = Groupteacher::getGroupTeacherListSimple($gid);
        
        // проверяем роль пользователя, занятие в журнал могут добавить только преподаватели назначенные в группу, менеджеры или руководители
        if (in_array($roleId, [3, 4, 10]) || in_array($userId, [296, 389]) || array_key_exists($teacherId, $groupTeachers)) {
            // создаем новую пустую модель
            $model = new Journalgroup();
		
            // получаем список текущих студентов в группе
            $students = (new Query())
            ->select('s.id, s.name')
            ->from('calc_studgroup sg')
            ->leftJoin('calc_studname s', 's.id=sg.calc_studname')
            ->where('sg.calc_groupteacher=:gid and s.visible=1 and sg.visible=1', [':gid'=>$gid])
            ->orderby(['s.name' => SORT_ASC])
            ->all();

            // если пришли данные и моделька загрузилась успешно, переходим в картоку преподавателя
            if ($model->load(Yii::$app->request->post())) {
                // если занятие добавляет преподаватель группы (не менеджер или руководитель)
                if (!(in_array($roleId, [3, 4]) || in_array($userId, [296, 389])) && array_key_exists($teacherId, $groupTeachers)) {
                    // выставляем учебное время как вечернее (2)
                    $model->calc_edutime = 2;
                }
                // если занятие добавляет не руководитель (и ольга воронецкая)
                if (!in_array($roleId, [3, 10]) && !in_array($userId, [62, 296])) {
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
                if (!$model->calc_teacher && count($groupTeachers) == 1) {
                    // пишем id преподавателя из ранее полученного списка преподавателей
                    $keys = array_keys($groupTeachers);
                    $model->calc_teacher = reset($keys);
                }
                $model->calc_groupteacher = $gid;
                $postData = Yii::$app->request->post('Studjournalgroup');
                if (!empty($postData)) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if (!$model->save()) {
                            throw new Exception('Не удалось добавить занятие!');
                        }
                        $studentsData = $this->convertPostData($postData);
                        foreach ($studentsData as $student) {
                            $studentRecord = new Studjournalgroup([
                                'calc_groupteacher'  => $gid,
                                'calc_journalgroup'  => $model->id,
                                'calc_studname'      => (int)$student['id'],
                                'calc_statusjournal' => $student['status'],
                                'successes'          => $model->type === Journalgroup::TYPE_ONLINE ? $student['successes'] : 0,
                                'comments'           => $student['comment'],
                            ]);
                            if (!$studentRecord->save()) {
                                throw new Exception('Не удалось создать запись о присутствии.');
                            }
                        }
                        Yii::$app->session->setFlash('success', 'Запись о занятии успешно добавлена в журнал.');
                        $transaction->commit();

                        return $this->redirect(['groupteacher/view', 'id' => $gid]);
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Не удалось добавить занятие!');

                        return $this->redirect(['groupteacher/view', 'id' => $gid]);
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось добавить занятие!');

                    return $this->redirect(['groupteacher/view', 'id' => $gid]);
                }
            } else {
                return $this->render('create', [
                    'group'          => $group,
                    'model'          => $model,
                    'params'         => $params,
                    'students'       => $students,
                    'teachers'       => $groupTeachers,
                    'timeHints'      => Journalgroup::getLastLessonTimesByGroup($gid),
                    'userId'         => $userId,
                    'userInfoBlock'  => User::getUserInfoBlock(),
                ]);
            }
        } else {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
    }

    /**
     * метод позволяет менеджерам, руководителям
     * и преподавателям назначенным в группу
     * редактировать запись о занятии в журнале
     * @param $id
     * @param $gid
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $gid)
    {
        $roleId = (int)Yii::$app->session->get('user.ustatus');
        $userId = (int)Yii::$app->session->get('user.uid');
        $teacherId = (int)Yii::$app->session->get('user.uteacher');
        /** @var Groupteacher $group */
        $group = Groupteacher::findOne($gid);
        if (empty($group)) {
            throw new NotFoundHttpException("Группа №{$gid} не найдена.");
        }
        $params['gid'] = $gid;
        $params['active'] = $group->visible ?? null;
        // получаем массив со списком преподавателей назначенных группе
        $groupTeachers = Groupteacher::getGroupTeacherListSimple($gid);
        // находим запись о занятии
        $model = $this->findModel($id);        

        if (in_array($roleId, [3, 4, 10]) || in_array($userId, [296, 389]) || array_key_exists($teacherId, $groupTeachers)) {
            // получаем данные из формы и обновляем запись
            if ($model->load(Yii::$app->request->post())) {
                // если занятие обновляет не руководитель (и не ольга воронецкая)
                if (!in_array($roleId, [3, 10]) && !in_array($userId, [62, 296])) {
                    // проверяем что со времени проведения занятия прошло не более 3 дней
                    $dt = new \DateTime('-5 days');
                    if($model->data <= $dt->format('Y-m-d')) {
                        // если более 3 дней, задаем сообщение об ошибке
                        Yii::$app->session->setFlash('error', 'Не удалось обновить занятие в журнале. С указанной даты прошло более 3 дней. Пожалуйста обратитесь к руководителю.');
                        // возвращаемся в журнал
                        return $this->redirect(['groupteacher/view', 'id' => $gid]);
                    }
                }
                if ($model->save()) {
                    // если модель сохранилась, задаем сообщение об успешном изменении занятия
                    Yii::$app->session->setFlash('success', 'Информация о занятии успешно обновлена!');
				} else {
                    // если модель не сохранилась, задаем сообщение об безуспешном изменении занятия
                    Yii::$app->session->setFlash('error', 'Не удалось обновить информацию о занятии!');
				}
                return $this->redirect(['groupteacher/view', 'id' => $gid]);
            }
			
            return $this->render('update', [
                'group'         => $group,
                'model'         => $model,
                'params'        => $params,
                'teachers'      => $groupTeachers,
                'timeHints'     => Journalgroup::getLastLessonTimesByGroup($gid),
                'userId'        => $userId,
                'userInfoBlock' => User::getUserInfoBlock(),
            ]);
        } else {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
    }

    /**
     * метод позволяет менеджерам отредактировать состав студентов
     * посетивших или пропустивших занятие
     * @param $id
     * @param $gid
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionChange($id, $gid)
	{
	    if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4])) {
	        throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

	    $lessonModel = $this->findModel($id);
	    if ((int)$gid !== $lessonModel->calc_groupteacher) {
            $gid = $lessonModel->calc_groupteacher;
        }
        $group = Groupteacher::find()->andWhere(['id' => $gid])->one();

        if (empty($group)) {
            throw new NotFoundHttpException("Группа №{$gid} не найдена.");
        }

	    $students = (new Query())
	    ->select([
	        'id'        => 's.id',
            'name'      => 's.name',
            'status'    => 'sjg.calc_statusjournal',
            'successes' => 'sjg.successes',
            'comment'   => 'sjg.comments',
            'date'      => 'sjg.data',
            'user'      => 'u.name',
        ])
	    ->from(['jg' => Journalgroup::tableName()])
	    ->leftJoin(['sjg' => Studjournalgroup::tableName()], 'jg.id = sjg.calc_journalgroup')
	    ->leftJoin(['s' => Student::tableName()], 's.id = sjg.calc_studname')
	    ->leftjoin(['u' => User::tableName()], 'jg.user = u.id')
	    ->where([
	        'jg.calc_groupteacher' => $gid,
            'jg.id'                => $id,
        ])
	    ->orderby(['s.name' => SORT_ASC])
	    ->all();

		// получаем историю статусов студентов
		$history = (new Query())
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
        $students = array_filter($students, function ($student) {
            return !empty($student['id']);
        });
	    if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('Studjournalgroup', []);
			if (!empty($postData)) {
				$studentsData = $this->convertPostData($postData);

				$oldstatuses = (new Query())
				->select('*')
				->from(Studjournalgroup::tableName())
				->where(['calc_journalgroup' => $id])
				->all();

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $timestamp = time();
                    foreach ($oldstatuses as $os) {
                        $db = (new Query())
                            ->createCommand()
                            ->insert('calc_studjournalgrouphistory', [
                                'calc_groupteacher'  => $os['calc_groupteacher'],
                                'calc_journalgroup'  => $os['calc_journalgroup'],
                                'calc_studname'      => $os['calc_studname'],
                                'calc_statusjournal' => $os['calc_statusjournal'],
                                'comments'           => $os['comments'],
                                'data'               => $os['data'],
                                'user'               => $os['user'],
                                'timestamp_id'       => $timestamp,
                            ])
                            ->execute();
                        /** @var Studjournalgroup $model */
                        if (($model = Studjournalgroup::find()->andWhere(['id' => $os['id']])->one()) !== NULL) {
                            if (!$model->delete()) {
                                throw new Exception('Не удалось удалить запись о присутствии.');
                            }
                        }
                    }

                    foreach ($studentsData ?? [] as $student) {
                        $model = new Studjournalgroup([
                            'calc_groupteacher'  => $gid,
                            'calc_journalgroup'  => $id,
                            'calc_studname'      => (int)$student['id'],
                            'calc_statusjournal' => $student['status'],
                            'successes'          => $lessonModel->type === Journalgroup::TYPE_ONLINE ? $student['successes'] : 0,
                            'comments'           => $student['comment'],
                        ]);
                        if (!$model->save()) {
                            throw new Exception('Не удалось создать запись о присутствии.');
                        }
                        if (in_array($student['status'], [1, 3])) {
                            if (($studentModel = Student::find()->andWhere(['id' => $student['id']])->one()) !== NULL) {
                                $studentModel->updateInvMonDebt();
                            }
                        }
                    }
                    Yii::$app->session->setFlash('success', 'Информация о составе занятия успешно обновлена.');
                    $transaction->commit();
                } catch (Exception $e) {
                    Yii::$app->session->setFlash('error', 'Не удалось обновить информацию о составе занятия.');
                    $transaction->rollBack();
                }
			} else {
                Yii::$app->session->setFlash('error', 'Нет информации для изменения.');
            }

			return $this->redirect(['groupteacher/view', 'id' => $gid]);	        
	    } else {
	        return $this->render('change', [
                'checkTeachers' => Groupteacher::getGroupTeacherListSimple($gid),
                'dates'         => $dates,
                'group'         => $group,
                'history'       => $history,
                'params'        => [
                    'active'    => $group->visible ?? null,
                    'gid'       => (int)$gid,
                    'lid'       => (int)$id,
                    'successes' => $lessonModel->type === Journalgroup::TYPE_ONLINE,
                ],
				'students'      => $students,
                'userInfoBlock' => User::getUserInfoBlock(),
    		]);
		}
    }

    /**
     * Помечает занятие как проверенное
     * @param int $gid
     * @param int $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($gid, $id)
	{
        if (!(in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4]) ||
           (int)Yii::$app->user->identity->id === 296)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
		
        $model = $this->findModel($id);
		
		$students = (new Query())
		->select([
            'id'        => 'sjg.calc_studname',
            'name'      => 'sn.name',
            'serviceId' => 'gt.calc_service'
        ])
		->from(['jg' => Journalgroup::tableName()])
		->leftjoin(['sjg' => 'calc_studjournalgroup'],   'sjg.calc_journalgroup = jg.id')
		->leftjoin(['gt'  => Groupteacher::tableName()], 'gt.id = jg.calc_groupteacher')
		->leftjoin(['sn'  => Student::tableName()],      'sn.id = sjg.calc_studname')
		->where([
            'jg.id'                  => $id,
            'jg.calc_groupteacher'   => $gid,
            'sjg.calc_statusjournal' => [
                Journalgroup::STUDENT_STATUS_PRESENT,
                Journalgroup::STUDENT_STATUS_ABSENT_UNWARNED,
            ],
        ])
		->all();

		$i = 0;
		$snames = [];
		foreach($students as $lessonStudent) {
            $student = Student::findOne($lessonStudent['id']);
            if (empty($student)) {
                Yii::$app->session->setFlash('error', 'Студент ' . $lessonStudent['name'] . ' не найден');
                return $this->redirect(['groupteacher/view', 'id' => $gid]);
            }
            $services = $student->getServicesBalance([$lessonStudent['serviceId']], []);
			if (empty($services) || $services[0]['num'] <= 0) {
				$snames[] = $lessonStudent['name'];
			}
			$i++;
		}
		if ((int)$model->view !== 1 && empty($snames)) {
			if ($model->view()) {
				Yii::$app->session->setFlash('success', 'Занятие успешно переведено в проверенные.');
			}
		} else {
			Yii::$app->session->setFlash('error', 'Невозможно проверить занятие. Выставите счета студентам: ' . join(', ', $snames) . '.');
		}

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Снимает с занятия отметку о проверке
     * @param int $gid
     * @param int $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUnview($gid, $id)
	{
        if (!(in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4]) ||
           (int)Yii::$app->user->identity->id === 296)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
		
		$model = $this->findModel($id);
        if($model->view == 1) {
		    if ($model->unview()) {
                Yii::$app->session->setFlash('success', 'Занятие успешно возвращено в непроверенные.');
			}
        }
        
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * метод позволяет преподавателю назначенному в группу,
     * менеджеру или руководителю,
     * исключить запись о занятии из журнала
     * @param $gid
     * @param $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($gid, $id)
    {
        $group = Groupteacher::findOne($gid);
        // находим запись по id
        $model = $this->findModel($id);

        // получаем массив со списком преподавателей назначенных группе
        $teachers = (new Query())
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
			$tmp_students = (new Query())
			->select('sjg.calc_studname as id, sjg.calc_statusjournal as status')
			->from('calc_studjournalgroup sjg')
			->where('sjg.calc_journalgroup=:sjid', [':sjid'=>$model->id])
			->all();
			//var_dump($tmp_students);die();
			foreach ($tmp_students as $student) {
				// апдейтим баланс клиента
				if (in_array($student['status'], [1, 3])) {
                    if (($studentModel = Student::find()->andWhere(['id' => $student['id']])->one()) !== NULL) {
                        $studentModel->updateInvMonDebt();
                    }
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
     * @param $gid
     * @param $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
	
    public function actionRestore($gid, $id)
    {
        $group = Groupteacher::findOne($gid);	
        // находим запись по id
        $model = $this->findModel($id);

        // получаем массив со списком преподавателей назначенных группе
        $teachers = (new Query())
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
			$tmp_students = (new Query())
			->select('sjg.calc_studname as id, sjg.calc_statusjournal as status')
			->from('calc_studjournalgroup sjg')
			->where('sjg.calc_journalgroup=:sjid', [':sjid'=>$id])
			->all();
			foreach ($tmp_students as $s) {
				if (in_array($s['status'], [1, 3])) {
					$student = Student::findOne($s['id']);
					$student->updateInvMonDebt();
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
     *
     * @param $gid
     * @param $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
    public function actionRemove($gid, $id)
	{
        if ((int)Yii::$app->session->get('user.ustatus') !== 3) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $db = (new Query())->createCommand()
            ->delete(Journalgroup::tableName(), 'id=:lid')
            ->bindParam(':lid',$id)
            ->execute();
        $db = (new Query())->createCommand()
            ->delete(Studjournalgroup::tableName(), 'calc_journalgroup=:lid')
            ->bindParam(':lid',$id)
            ->execute();
        $db = (new Query())->createCommand()
            ->delete('calc_studjournalgrouphistory', 'calc_journalgroup=:lid')
            ->bindParam(':lid',$id)
            ->execute();

        return $this->redirect(['groupteacher/view', 'id' => $gid]);
    }

    /**
     *  метод позволяет менеджерам и руководителям
     *  изменить посещение урока учеником с "Не было"
     *  на "Не было (предупредил)"
     * @param int $id
     * @param int $studentId
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    public function actionAbsent($id, $studentId)
    {
        if(!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        /** @var Studjournalgroup $model */
        $model = Studjournalgroup::find()->andWhere([
            'calc_journalgroup' => $id,
            'calc_studname' => $studentId
        ])->one();

        if (!empty($model)) {
            $t = Yii::$app->db->beginTransaction();
            try {
                // переносим копию записи в таблицу с историей изменений
                $db = (new Query())
                ->createCommand()
                ->insert('calc_studjournalgrouphistory', [
                    'calc_groupteacher'  => $model->calc_groupteacher,
                    'calc_journalgroup'  => $model->calc_journalgroup,
                    'calc_studname'      => $model->calc_studname,
                    'calc_statusjournal' => $model->calc_statusjournal,
                    'comments'           => $model->comments,
                    'data'               => $model->data,
                    'user'               => $model->user,
                    'timestamp_id'       => time(),
                ])
                ->execute();

                // удаляем запись о посещении занятия студентом
                $model->delete();

                /* пишем данные о посещаемости в базу */
                $studentRecord = new Studjournalgroup([
                    'calc_groupteacher'  => $model->calc_groupteacher,
                    'calc_journalgroup'  => $model->calc_journalgroup,
                    'calc_studname'      => $model->calc_studname,
                    'calc_statusjournal' => Journalgroup::STUDENT_STATUS_ABSENT_WARNED,
                    'successes'          => $model->successes,
                    'comments'           => $model->comments,
                ]);
                if (!$studentRecord->save()) {
                    throw new Exception('Не удалось создать запись о присутствии.');
                }
                Yii::$app->session->setFlash('success', 'Информация о посещении занятия учеником успешно обновлена.');
                $t->commit();
            } catch (Exception $e) {
                $t->rollBack();
                Yii::$app->session->setFlash('success', 'Не удалось обновить информация о посещении занятия учеником.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось найти информацию о посещении занятия.');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Journalgroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     *
     * @return Journalgroup the loaded model
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
		$invoices_sum = (new Query())
        ->select('sum(value) as money')
        ->from('calc_invoicestud')
		->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
        ->one();
		
		// получаем информацию по оплатам
		$payments_sum = (new Query())
        ->select('sum(value) as money')
        ->from('calc_moneystud')
		->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
        ->one();
		
		// считаем разницу как базовый долг
		$debt_common = $payments_sum['money'] - $invoices_sum['money'];
		
		// запрашиваем услуги назначенные студенту
		$services = (new Query())
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
				$lessons = (new Query())
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
						$lesson_cost = (new Query())
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

    /**
     * @param $postData
     * @return array
     */
	private function convertPostData($postData)
    {
        $studentsData = [];

        foreach ($postData ?? [] as $key => $value) {
            $keys = explode('_', $key);
            if (!empty($keys) && isset($keys[0]) && isset($keys[1])) {
                if (!isset($studentsData[$keys[1]])) {
                    $studentsData[$keys[1]] = [
                        'id' => $keys[1],
                    ];
                }
                $studentsData[$keys[1]][$keys[0]] = $value;
            }
        }

        return $studentsData;
    }
}
