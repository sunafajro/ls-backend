<?php

namespace school\controllers;

use school\models\Eduage;
use school\models\EducationLevel;
use school\models\Groupteacher;
use school\models\Journalgroup;
use school\models\Lang;
use school\models\Office;
use school\models\Student;
use school\models\Studgroup;
use school\models\Studjournalgroup;
use school\models\Teacher;
use school\models\Teachergroup;
use school\models\forms\UploadForm;
use school\models\Auth;
use school\models\GroupFile;
use school\models\searches\GroupFileSearch;
use school\models\User;
use school\models\searches\GroupSearch;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class GroupteacherController extends Controller
{
    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        $rules = [
            'index', 'view', 'create', 'update',
            'status', 'addteacher', 'delteacher',
            'restoreteacher', 'addstudent', 'delstudent',
            'restorestudent', 'change-params',
            'announcements', 'files',
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

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        // TODO проверка доступа
        $searchModel = new GroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        $levels = EducationLevel::find()->active()->orderBy(['name' => SORT_ASC])->all();
        $levels = ArrayHelper::map($levels, 'id', 'name');

        return $this->render('index', [
            'ages'          => Eduage::getEduAgesSimple(),
            'dataProvider'  => $dataProvider,
            'levels'        => $levels,
            'languages'     => Lang::getLanguagesSimple(),
            'offices'       => Office::getOfficesListSimple(),
            'searchModel'   => $searchModel,
        ]);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView(string $id)
    {
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        $group  = $this->findModel(intval($id));

        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4, 5, 6]) !== 3 && ($roleId === 10 && $group->company !== 2)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $lid     = NULL;
        $state   = NULL;
        $checked = NULL;
        $payed   = NULL;
        $deleted = NULL;
        $page    = NULL;

        // задаем дефолтный лимит по количеству занятий
        $limit  = 5;
        $offset = 0;
        if ((int)Yii::$app->request->get('page', null) > 1) {
            $offset = 5 * ((int)Yii::$app->request->get('page') - 1);
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
            'group'         => $group,
            'groupStudents' => $groupStudents,
            'groupTeachers' => array_keys(Groupteacher::getGroupTeacherListSimple($id)),
            'lessons'       => $lessons,
            'lid'           => $lid,
            'pages'         => $pages,
            'page'          => $page,
            'state'         => $state,
            'students'      => $students,
            'lesattend'     => $lesattend,
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
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4])) {
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
                Yii::$app->session->setFlash('success', "Успешно создана группа #$model->id!");
            } else {
                Yii::$app->session->setFlash('error', "Не удалось создать группу!");
            }
            return $this->redirect(['teacher/view', 'id' => $tid, 'tab'=>1]);
        } else {
            // если нет данных, выводим переменные в вьюз создания группы
            return $this->render('create', [
                'model'    => $model,
				'teacher'  => $teacher,
				'services' => $services,
				'levels'   => $levels,
				'offices'  => $offices,
            ]);
        }
    }

    /**
     * @param $id
     * @param $lid
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
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
     * @param $gid
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAddteacher($gid)
	{
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

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
				'groupTeachers' => array_keys(Groupteacher::getGroupTeacherListSimple($gid)),
                'curteachers'   => $curteachers,
                'group'         => $group,
                'model'         => $model,
                'params'        => $params,
                'teachers'      => Groupteacher::getTeacherListSimple($gid),
			]);
		}
    }

    /**
     * @param $gid
     * @param $tid
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionDelteacher($gid, $tid)
	{
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $check = Teachergroup::find()->where('visible=:vis and calc_groupteacher=:gid and calc_teacher!=:tid', [':vis'=>1, ':gid'=>$gid, ':tid'=>$tid])->all();
        if (!empty($check)) {
            $model = Teachergroup::find()->where('visible=:vis and calc_groupteacher=:gid and calc_teacher=:tid', [':vis'=>1, ':gid'=>$gid, ':tid'=>$tid])->one();
            if ($model != NULL) {
                $model->visible = 0;
                if($model->save()) {
                     Yii::$app->session->setFlash('success', 'Преподаватель успешно удален из группы!');
                } else {
                     Yii::$app->session->setFlash('error', 'Неудалось удалить преподавателя!');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Неудалось удалить преподавателя! Преподаватель не является активным для группы.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Неудалось удалить преподавателя! В группе нет других активных преподавателей.');
        }

		return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $gid
     * @param $tid
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionRestoreteacher($gid, $tid)
    {
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $model = Teachergroup::find()->where('visible=:vis and calc_groupteacher=:gid and calc_teacher=:tid', [':vis'=>0, ':gid'=>$gid, ':tid'=>$tid])->one();
        if ($model != NULL) {
            $model->visible = 1;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Преподаватель успешно восстановлен в группу!');
            } else {
                Yii::$app->session->setFlash('error', 'Неудалось восстановить преподавателя в группу!');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Неудалось восстановить преподавателя! Преподаватель не состоит в группе.');
        }

		return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $gid
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAddstudent($gid)
    {
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

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
				'groupTeachers'  => array_keys(Groupteacher::getGroupTeacherListSimple($gid)),
                'group'          => $group,
                'params'         => $params,
			]);
		}
	}

    /**
     * @param $gid
     * @param $sid
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
	public function actionDelstudent($gid, $sid)
	{
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4])) {
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
     * @param $gid
     * @param $sid
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
	public function actionRestorestudent($gid, $sid)
	{
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

	    // меняем параметр видимости на 1
	    $db = (new \yii\db\Query())
		->createCommand()
		->update('calc_studgroup', ['visible'=>1], 'calc_groupteacher=:gid AND calc_studname=:sid')
		->bindParam(':gid',$gid)
        ->bindParam(':sid',$sid)
		->execute();
		// возвращаемся обратно
		return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Изменяет параметры группы: офис, уровень, корпоративный статус, основной преподаватель.
     * @param integer $id
     * @param string $name
     * @param integer $value
     *
     * @return mixed
     * @throws NotFoundHttpException|ForbiddenHttpException
     */
    public function actionChangeParams(int $id, string $name, int $value)
    {
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

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
     * @param $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAnnouncements($id)
    {
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4, 5])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $group = $this->findModel($id);

        return $this->render('announcements', [
            'group'         => $group,
            'groupTeachers' => array_keys(Groupteacher::getGroupTeacherListSimple($id)),
        ]);
    }

    /**
     * @param string $id
     * @param string|null $action
     * @param string|null $file_id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function actionFiles(string $id, string $action = null, string $file_id = null)
    {
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        // TODO переделать в поведения
        if (!in_array($roleId, [3, 4, 5, 6, 10])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $group = $this->findModel($id);

        if (in_array($action, ['download', 'delete']) && $file_id) {
            $file = GroupFile::find()->byId(intval($file_id))->byEntityId(intval($group->id))->one();
            if (empty($file)) {
                throw new NotFoundHttpException(Yii::t('app', 'File not found!'));
            }
            if ($action === 'delete') {
                if ($file->delete()) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'File successfully deleted!'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete file!'));
                }
                return $this->redirect(['groupteacher/files', 'id' => $group->id]);
            } else if ($action === 'download') {
                return Yii::$app->response->sendFile($file->getPath(), $file->original_name, ['inline' => true]);
            }
        }

        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                if ($model->saveFile(GroupFile::getTempDirPath())) {
                    $file = new GroupFile([
                        'file_name'     => $model->file_name,
                        'original_name' => $model->original_name,
                        'size'          => $model->file->size,
                    ]);
                    if ($file->save()) {
                        $file->setEntity(GroupFile::DEFAULT_ENTITY_TYPE, $group->id);
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', 'File successfully uploaded!'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to upload file!'));
                }
                return $this->redirect(['groupteacher/files', 'id' => $group->id]);
            }
        }

        $searchModel = new GroupFileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get(), $group->id);

        return $this->render('files', [
            'dataProvider'  => $dataProvider,
            'group'         => $group,
            'groupTeachers' => array_keys(Groupteacher::getGroupTeacherListSimple($id)),
            'searchModel'   => $searchModel,
            'uploadForm'    => new UploadForm(),
        ]);
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
