<?php

namespace app\modules\school\controllers;

use app\models\Eduage;
use app\models\Edulevel;
use app\models\Groupteacher;
use app\models\Journalgroup;
use app\models\Lang;
use app\models\Office;
use app\models\Student;
use app\models\Studgroup;
use app\models\Studjournalgroup;
use app\models\Teacher;
use app\models\Teachergroup;
use app\modules\school\models\User;
use app\models\search\GroupSearch;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class GroupteacherController extends Controller
{
    public function behaviors()
    {
        $rules = [
            'index', 'view', 'create', 'update',
            'status', 'addteacher', 'delteacher',
            'restoreteacher', 'addstudent', 'delstudent',
            'restorestudent', 'change-params',
        ];
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
                'class'   => VerbFilter::class,
                'actions' => [
                    'change-params' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new GroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        return $this->render('index', [
            'ages'          => Eduage::getEduAgesSimple(),
            'dataProvider'  => $dataProvider,
            'levels'        => Edulevel::getEduLevelsSimple(),
            'languages'     => Lang::getLanguagesSimple(),
            'offices'       => Office::getOfficesListSimple(),
            'searchModel'   => $searchModel,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * Displays a single Groupteacher model.
     * @param integer $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $roleId = (int)Yii::$app->session->get('user.ustatus');
        /** @var Groupteacher $group */
        $group = $this->findModel($id);

        /* проверяем права доступа (! переделать в поведения !) */
        if(!in_array($roleId, [3, 4, 5, 6]) !== 3 && ($roleId === 10 && $group->company !== 2)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $lid     = NULL;
        $state   = NULL;
        $checked = NULL;
        $payed   = NULL;
        $deleted = NULL;
        $page    = NULL;

        // задаем дефолтный лимит по количеству занятий
        $limit = 5;
        $offset = 0;
        if ((int)Yii::$app->request->get('page', null) > 1) {
            $offset = 5 * ((int)Yii::$app->request->get('page') - 1);
        } else {
            $offset = 0;
        }

        if (Yii::$app->request->get('page', false)) {
            $page = (int)Yii::$app->request->get('page');
        } else {
            $page = 1;
        }

        if (Yii::$app->request->get('lid', false)) {
            $lid = Yii::$app->request->get('lid');
        }

        if (Yii::$app->request->get('status')) {
            $state = (int)Yii::$app->request->get('status');
            switch ($state) {
                case 1: $checked = 0; $payed = 0; $deleted = 1; break;
                case 2: $checked = 1; $payed = 0; $deleted = 1; break;
                case 3: $checked = 1; $payed = 1; $deleted = 1;  break;
                case 4: $deleted = 0; break;
            }
        }

        $ut = User::tableName();
        // выбираем занятия
        $lessons = (new \yii\db\Query())
        ->select([
            'jid'          => 'jg.id',
            'jdate'        => 'jg.data',
            'time_begin'   => 'jg.time_begin',
            'jdesc'        => 'jg.description',
            'jhwork'       => 'jg.homework',
            'jvisible'     => 'jg.visible',
            'uname'        => 'u.name',
            'visible_date' => 'jg.data_visible',
            'jvuser'       => 'u2.name',
            'edit_date'    => 'jg.data_edit',
            'jeuser'       => 'u3.name',
            'done_date'    => 'jg.data_done',
            'jdone'        => 'jg.done',
            'jduser'       => 'u4.name',
            'view_date'    => 'jg.data_view',
            'jview'        => 'jg.view',
            'view_user'    => 'u5.name',
            'accrual'      => 'jg.calc_accrual',
            'tname'        => 't.name',
            'edutime'      => 'jg.calc_edutime',
            'type'         => 'jg.type',
        ])
        ->from(['jg' => Journalgroup::tableName()])
        ->leftJoin(['t' => Teacher::tableName()], 't.id = jg.calc_teacher')
        ->leftJoin(['u'  => $ut], 'u.id = jg.user')
        ->leftJoin(['u2' => $ut], 'u2.id=jg.user_visible')
        ->leftJoin(['u3' => $ut], 'u3.id=jg.user_edit')
        ->leftJoin(['u4' => $ut], 'u4.id=jg.user_done')
        ->leftJoin(['u5' => $ut], 'u5.id=jg.user_view')
        ->where(['jg.calc_groupteacher' => $id])
        ->andWhere(['>', 'jg.user', 0])
        ->andFilterWhere(['jg.id'      => $lid])
        ->andFilterWhere(['jg.visible' => $deleted])
        ->andFilterWhere(['jg.view'    => $checked])
        ->andFilterWhere(['jg.done'    => $payed]);

        // делаем клон запроса
        $countQuery = clone $lessons;
        // получаем данные для паджинации
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        //завершаем запрос
        $lessons = $lessons->orderby(['jg.data'=>SORT_DESC])->limit($limit)->offset($offset)->all();

        $list= ArrayHelper::getColumn($lessons,'jid');
        if (!empty($list)) {
	    // выбираем посещения занятий
	    $students = (new \yii\db\Query())
	    ->select(['jid' => 'jg.id', 'sid' => 's.id', 'sname' => 's.name', 'status' => 'sjg.calc_statusjournal', 'successes' => 'sjg.successes'])
	    ->from(['jg' => Journalgroup::tableName()])
	    ->leftJoin(['sjg' => Studjournalgroup::tableName()], 'sjg.calc_journalgroup = jg.id')
	    ->leftJoin(['s' => Student::tableName()], 's.id = sjg.calc_studname')
	    ->where(['in', 'jg.id', $list])
	    ->orderby(['jg.id' => SORT_DESC, 'sjg.calc_statusjournal' => SORT_ASC])
	    ->all();

	    $lesattend = [];
	    foreach ($students as $student) {
	        switch ($student['status']) {
                case 1:
                    $lesattend[$student['jid']]['id']=$student['jid'];
                    $lesattend[$student['jid']]['p'] = 1;
                    break;
                case 2:
                    $lesattend[$student['jid']]['id']=$student['jid'];
                    $lesattend[$student['jid']]['a1'] = 1;
                    break;
                case 3:
                    $lesattend[$student['jid']]['id']=$student['jid'];
                    $lesattend[$student['jid']]['a2'] = 1;
                    break;
	        }
	     }
        } else {
            $students = [];
            $lesattend = [];
        }

        $groupStudents = ArrayHelper::map($students, 'sid', 'sname');
        foreach ($groupStudents as $studentId => $studentName) {
            $student = Student::findOne($studentId);
            if (!empty($student)) {
                $data = $student->getServicesBalance([$group->calc_service], []);
                $groupStudents[$studentId] = $data[0]['num'] ?? 0;
            } else {
                $groupStudents[$studentId] = 0;
            }
        }

        return $this->render('view', [
            'model'         => $this->findModel($id),
            'groupStudents' => $groupStudents,
            'lessons'       => $lessons,
            'lid'           => $lid,
            'pages'         => $pages,
            'page'          => $page,
            'state'         => $state,
			'checkTeachers' => Groupteacher::getGroupTeacherListSimple($id),
            'students'      => $students,
            'lesattend'     => $lesattend,
            'items'         => Groupteacher::getMenuItemList($id, Yii::$app->controller->id . '/' . Yii::$app->controller->action->id),
            'userInfoBlock' => User::getUserInfoBlock(),
            'jobPlace'      => [ 1 => 'ШИЯ', 2 => 'СРР' ]
        ]);
    }

    /**
     * @param int $tid
     * @return mixed
     *
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
    public function actionCreate($tid)
    {
        /* проверяем права доступа (! переделать в поведения !) */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

		// создаем новую модель
        $model = new Groupteacher();
		// получаем фио преподавателя по его id, для послед. использования
		$teacher = (new \yii\db\Query())
		->select('id, name')
		->from('calc_teacher')
		->where('id=:id', [':id'=>$tid])
		->one();

		/* получаем список доступных преподавателю услуг */
		$tmp_services = (new \yii\db\Query())
		->select('s.id as id, s.name as name')
		->from('calc_service s')
		->leftjoin('calc_langteacher lt', 's.calc_lang=lt.calc_lang')
		->where('s.visible=:vis AND lt.visible=:vis AND lt.calc_teacher=:tid AND s.data>:date', [':vis'=>1, ':tid'=>$tid, ':date'=>date('Y-m-d')])
		->orderby(['s.id'=>SORT_ASC, 's.calc_lang'=>SORT_ASC,'s.calc_eduform'=>SORT_ASC])
		->all();

        $tmp = [];
        // готовим массив для селекта услуг
        foreach($tmp_services as $s) {
            $tmp[$s['id']] = $s['id']." - ".$s['name'];
        }
        $services = array_unique($tmp);
        unset($tmp);
        unset($tmp_services);
        /* получаем список доступных преподавателю услуг */

        /* если нет доступных услуг, выводим предупреждение */
        if(empty($services)) {
            // задаем сообщеие об ошибке
            Yii::$app->session->setFlash('error', 'Не удалось создать группу! Проверьте наличие доступных языков.');
            return $this->redirect(['teacher/view', 'id'=>$tid]);
        }

		/* получаем список уровней языка для селекта */
		$tmp_levels = (new \yii\db\Query())
		->select('id, name')
		->from('calc_edulevel')
                ->where('visible=:one', [':one' => 1])
                ->orderBy(['name' => SORT_ASC])
		->all();

        $tmp = [];
        /* готовим массив для селекта уровней */
        foreach($tmp_levels as $l){
            $tmp[$l['id']] = $l['name'];
        }
        $levels = array_unique($tmp);
        unset($tmp);
        unset($tmp_levels);
        /* получаем список уровней языка для селекта */

		/* получаем список офисов для селекта */
		$tmp_offices = (new \yii\db\Query())
		->select('id, name')
		->from('calc_office')
		->where('visible=:one', [':one' => 1])
                ->orderBy(['name' => SORT_ASC])
		->all();

        $tmp = [];
        /* готовим массив для селекта офисов */
        foreach($tmp_offices as $o){
            $tmp[$o['id']] = $o['name'];
        }
        $offices = array_unique($tmp);
        unset($tmp);
        unset($tmp_offices);
        /* получаем список офисов для селекта */

		// если пришли данные и моделька сохранилась успешно, переходим в картоку преподавателя
        if ($model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            $model->calc_teacher = $tid;
            $model->data = date('Y-m-d');
            $model->user = Yii::$app->session->get('user.uid');
            if($model->save()) {
	        // теперь делаем запись о преподавателе и группе в табличку замен
	        $db = (new \yii\db\Query())
	        ->createCommand()
	        ->insert('calc_teachergroup', [
			'calc_groupteacher'=>$model->id,
			'calc_teacher'=>$model->calc_teacher,
			'date'=>$model->data,
			'user'=>$model->user,
			'visible'=>$model->visible,
			])
	        ->execute();
                // задаем сообщение об успешном добавлении группы
                Yii::$app->session->setFlash('success', "Успешно создана группа #$model->id!");
            } else {
                // задаем сообщение об успешном добавлении группы
                Yii::$app->session->setFlash('error', "Не удалось создать группу!");
            }
            return $this->redirect(['teacher/view', 'id' => $tid, 'tab'=>1]);
        } else {
            // если нет данных, выводим переменные в вьюз создания группы
            return $this->render('create', [
                'model' => $model,
				'teacher' => $teacher,
				'services' => $services,
				'levels' => $levels,
				'offices' => $offices,
                'userInfoBlock' => User::getUserInfoBlock(),
                'jobPlace' => [ 1 => 'ШИЯ', 2 => 'СРР' ]
            ]);
        }
    }

    /**
     * Функция для изменения состояния группы
     */
    public function actionStatus($id, $lid)
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        // получаем информацию по пользователю
        $model = $this->findModel($id);
        //проверяем текущее состояние
        if ((int)$model->visible === 0) {
            $model->visible = 1;
            $model->user_visible = 0;
            $model->data_visible = '0000-00-00';
            $model->save();
        } else {
            $model->visible = 0;
            $model->user_visible = Yii::$app->session->get('user.uid');
            $model->data_visible = date('Y-m-d');
            $model->save();
        }
        return $this->redirect(['teacher/view', 'id' => $lid]);
    }

    /**
     * функция добавления преподавателя в учебную группу
     */
    public function actionAddteacher($gid)
	{
        $group = $this->findModel($gid);

        $params['gid'] = $gid;
        $params['active'] = $group->visible ?? null;
        
		// создаем новую пустую модель
		$model = new Teachergroup();
		
		// получаем список текущих преподавателей группы
		$curteachers = (new \yii\db\Query())
		->select('t.id as id, t.name as teacher, tg.date as date, u.name as user, tg.visible as visible')
		->from('calc_teachergroup tg')
		->leftJoin('calc_teacher t', 't.id=tg.calc_teacher')
		->leftJoin(['u' => User::tableName()], 'u.id=tg.user')
		->where('tg.calc_groupteacher=:gid', [':gid'=>$gid])
		->orderby(['tg.date'=>SORT_DESC])
		->all();
          
		if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            $model->calc_groupteacher = $gid;
            $model->visible = 1;
            $model->date = date('Y-m-d');
            $model->user = Yii::$app->session->get('user.uid');
            if ((Teachergroup::find()->where('calc_teacher=:tid AND calc_groupteacher=:gid', 
                [':tid' => $model->calc_teacher, ':gid' => $model->calc_groupteacher])->one()) === NULL) {
                if($model->save()) {
                    Yii::$app->session->setFlash('success', 'Преподаватель успешно добавлен в группу!');
                } else {
                    Yii::$app->session->setFlash('error', 'Неудалось добавить преподавателя в группу!');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Преподаватель уже связан с данной группой!');
            }

		    return $this->redirect(['groupteacher/addteacher', 'gid' => $gid]);
		} else {
			return $this->render('addteacher', [
				'check_teachers' => Groupteacher::getGroupTeacherListSimple($gid, true),
                'curteachers'    => $curteachers,
                'group'          => $group,
                'items'          => Groupteacher::getMenuItemList($gid, Yii::$app->controller->id . '/' . Yii::$app->controller->action->id),
                'model'          => $model,
                'params'         => $params,
                'teachers'       => Groupteacher::getTeacherListSimple($gid),
                'userInfoBlock'  => User::getUserInfoBlock(),
			]);
		}
    }
	
    /**
     * функция исключения преподавателя из учебной группы
     */
    public function actionDelteacher($gid, $tid)
	{
        /* проверяем права доступа (! переделать в поведения !) */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        // проверяем есть ли другие активные преподаватели в группе
        $check = Teachergroup::find()->where('visible=:vis and calc_groupteacher=:gid and calc_teacher!=:tid', [':vis'=>1, ':gid'=>$gid, ':tid'=>$tid])->all();
        // если есть     
        if(!empty($check)) {
            // находим запись, заодно проверяем активная ли она
            $model = Teachergroup::find()->where('visible=:vis and calc_groupteacher=:gid and calc_teacher=:tid', [':vis'=>1, ':gid'=>$gid, ':tid'=>$tid])->one();
            // если нашли
            if($model != NULL) {
	        // меняем параметр видимости на 0
                $model->visible = 0;
                if($model->save()) {
                     // задаем сообщение об успехе
                     Yii::$app->session->setFlash('success', 'Преподаватель успешно удален из группы!');
                } else {
                     // задаем сообщение об ошибке
                     Yii::$app->session->setFlash('error', 'Неудалось удалить преподавателя!');
                }
            } else {
                // задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Неудалось удалить преподавателя! Преподаватель не является активным для группы.');
            }
        } else {
            // задаем сообщение об ошибке
            Yii::$app->session->setFlash('error', 'Неудалось удалить преподавателя! В группе нет других активных преподавателей.');
        }
		// возвращаемся обратно
		return $this->redirect(Yii::$app->request->referrer);
    }

    /** 
     * функция восстановления преподавателя в учебную группу
     */
    public function actionRestoreteacher($gid, $tid)
    {
        /* проверяем права доступа (! переделать в поведения !) */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        // находим запись, заодно проверяем неактивная ли она
        $model = Teachergroup::find()->where('visible=:vis and calc_groupteacher=:gid and calc_teacher=:tid', [':vis'=>0, ':gid'=>$gid, ':tid'=>$tid])->one();
        // если нашли
        if($model != NULL) {
            // меняем параметр видимости на 0
            $model->visible = 1;
            if($model->save()) {
                // задаем сообщение об успехе
                Yii::$app->session->setFlash('success', 'Преподаватель успешно восстановлен в группу!');
            } else {
                // задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Неудалось восстановить преподавателя в группу!');
            }
        } else {
            // задаем сообщение об ошибке
            Yii::$app->session->setFlash('error', 'Неудалось восстановить преподавателя! Преподаватель не состоит в группе.');
        }

		// возвращаемся обратно
		return $this->redirect(Yii::$app->request->referrer);
    }
	
    /**
     * функция добавления студента в учебную группу
     */
    public function actionAddstudent($gid)
    {
        $group = $this->findModel($gid);

        $params['gid'] = $gid;
        $params['active'] = $group->visible ?? null;
		// создаем новую модель
		$model = new Studgroup();

		// получаем массив со списком преподавателей назаначенных группе
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
		unset($tt);
		unset($teachers);
	
		// получаем список текущих студентов в группе
		$curstudents = (new \yii\db\Query())
		->select('s.id as id, s.name as name, sg.data as date, u.name as user, sg.visible as visible')
		->from('calc_studgroup sg')
		->leftJoin('calc_studname s', 's.id=sg.calc_studname')
		->leftJoin(['u' => User::tableName()], 'u.id = sg.user')
		->where('sg.calc_groupteacher=:gid', [':gid'=>$gid])
		->orderby(['sg.data'=>SORT_DESC])
		->all();
		
		// если пришли данные и моделька сохранилась успешно, переходим в картоку преподавателя
		if ($model->load(Yii::$app->request->post())) {
            /* проверяем права доступа (! переделать в поведения !) */
            if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            $model->calc_groupteacher = $gid;
            $model->visible = 1;
            $model->data = date('Y-m-d');
            $model->user = Yii::$app->session->get('user.uid');
            if (($check = Studgroup::find()->where('calc_groupteacher=:gid AND calc_studname=:sid', 
                [':gid' => $model->calc_groupteacher, ':sid' => $model->calc_studname])->one()) === NULL) {
                if($model->save()) {
                    /* сообщение об успехе */
                    Yii::$app->session->setFlash('success', 'Студент успешно добавлен в группу!');
                } else {
                /* сообщение об ошибке */
                Yii::$app->session->setFlash('error', 'Неудалось добавить студента в группу!');
                }
            } else {
                /* сообщение об ошибке */
                Yii::$app->session->setFlash('error', 'Студент уже связан с данной группой!');
            }

			return $this->redirect(['groupteacher/addstudent', 'gid' => $gid]);
		} else{
			return $this->render('addstudent', [
				'model'          => $model,
				'curstudents'    => $curstudents,
				'students'       => Groupteacher::getStudentListSimple($gid),
				'checkTeachers'  => Groupteacher::getGroupTeacherListSimple($gid, true),
                'group'          => $group,
                'items'          => Groupteacher::getMenuItemList($gid, Yii::$app->controller->id . '/' . Yii::$app->controller->action->id),
                'userInfoBlock'  => User::getUserInfoBlock(),
                'params'         => $params,
			]);
		}
	}

    /**
     *  функция исключения студента из учебной группы
     */
	public function actionDelstudent($gid, $sid)
	{
        /* проверяем права доступа (! переделать в поведения !) */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

		// меняем параметр видимости на 0
		$db = (new \yii\db\Query())
		->createCommand()
		->update('calc_studgroup', ['visible'=>0], 'calc_groupteacher=:gid AND calc_studname=:sid')
		->bindParam(':gid',$gid)->bindParam(':sid',$sid)
		->execute();
		// возвращаемся обратно
		return $this->redirect(Yii::$app->request->referrer);
    }
	
    /** 
     *  функция восстановления студента в учебную группу
     */
	public function actionRestorestudent($gid, $sid)
	{
        /* проверяем права доступа (! переделать в поведения !) */
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

	    // меняем параметр видимости на 1
	    $db = (new \yii\db\Query())
		->createCommand()
		->update('calc_studgroup', ['visible'=>1], 'calc_groupteacher=:gid AND calc_studname=:sid')
		->bindParam(':gid',$gid)->
		bindParam(':sid',$sid)
		->execute();
		// возвращаемся обратно
		return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Изменяет параметры группы: офис, уровень, корпоративный статус, основной преподаватель.
     * @param integer $id
     * @param string  $name
     * @param integer $value
     * 
     * @return mixed
     */
    public function actionChangeParams(int $id, string $name, int $value)
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        /** @var Groupteacher $group */
        $group = $this->findModel($id);
        if (!$group->hasAttribute($name)) {
            throw new NotFoundHttpException("Параметра группы - {$name}, не существует");
        }
        if (!in_array($name, ['calc_office', 'calc_edulevel', 'calc_teacher', 'corp'])) {
            throw new ForbiddenHttpException("Параметр группы - {$name}, не подлежит изменению.");
        }
        $group->$name = $value;

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($group->save(true, [$name])) {
            Yii::$app->session->setFlash('success', 'Параметры группы успешно изменены.');
            return [
                'status' => true,
            ];
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось изменить параметры группы.');
            Yii::$app->response->statusCode = 500;
            return [
                'status' => false,
            ];
        }
    }

    /**
     * Finds the Groupteacher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Groupteacher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Groupteacher::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException("Группа #{$id} не найдена");
        }
    }
}
