<?php

namespace app\modules\school\controllers;

use Yii;
use app\models\AccrualTeacher;
use app\models\Groupteacher;
use app\models\LanguagePremium;
use app\models\Service;
use app\models\Teacher;
use app\models\TeacherLanguagePremium;
use app\modules\school\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\web\ServerErrorHttpException;

/**
 * TeacherController implements the CRUD actions for CalcTeacher model.
 */
class TeacherController extends Controller
{
    public function behaviors()
    {
        return [
	    'access' => [
                'class' => AccessControl::className(),
                'only' => [
					'index',
					'view',
					'create',
					'update',
					'delete',
					'enable',
					'language-premiums',
					'delete-language-premium',
			    ],
                'rules' => [
                    [
                        'actions' => [
							'index',
							'view',
							'create',
							'update',
							'delete',
							'enable',
							'language-premiums',
							'delete-language-premium',
						],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
							'index',
							'view',
							'create',
							'update',
							'delete',
							'enable',
							'language-premiums',
							'delete-language-premium',
						],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Выводит список преподавателей школы.
     * @return mixed
     */
    public function actionIndex()
    {
	// пользователей с ролью преподавателя
        if((int)Yii::$app->session->get('user.ustatus') === 5 && (int)Yii::$app->session->get('user.uid') !== 296){
	    // редиректим в их собственные карточки карточку
            return $this->redirect(['view','id'=>Yii::$app->session->get('user.uteacher')]);
        }
	/* собираем массив параметров из url запроса */
	$arr = ['TOID' => NULL, 'TJID' => NULL, /* 'JPID' => NULL, */ 'TLID' => NULL, 'TSS' => NULL, 'STATE' => 0, 'BD' => NULL];
	$params = $this->getUrlParams($arr);

	if($params['BD']){
	    $sorting = ['MONTH(tch.birthdate)'=>SORT_ASC, 'DAY(tch.birthdate)'=>SORT_ASC, 'YEAR(tch.birthdate)'=>SORT_ASC, 'tch.name'=>SORT_ASC];
	} else {
	    $sorting = ['tch.name'=>SORT_ASC];
	}

        // создаем запрос на выборку преподавателей
        $teachers = (new \yii\db\Query())
        ->select('tch.id as tid, tch.name as tname, tch.email as temail, tch.phone as tphone, tch.birthdate as bd, tch.social_link as url, tch.value_corp as corp');
		// добавляем доп столбец в выборку для руководителей
		if($params['TJID']||Yii::$app->session->get('user.ustatus')==3){
		    $teachers = $teachers->addSelect(['tstjob'=>'tch.calc_statusjob']);
		}
		// если задан офис есть вероятность выдачи нескольких одинаковых записей
		if($params['TOID'] && $params['TOID'] !== 'all'){
			// во избежание дублирования ответов, делаем SELECT DISTINCT()
			$teachers = $teachers->distinct();
		}
		// подключаем основную талицу для выборки
        $teachers = $teachers->from('calc_teacher tch');
		// если задана переменная с id офиса
		if($params['TOID'] && $params['TOID'] !== 'all'){
			// добавляем таблицы для выборки преподавателей с учетом офиса 
			$teachers = $teachers
			->leftJoin('calc_teachergroup tg', 'tg.calc_teacher=tch.id')
			->leftJoin('calc_groupteacher gt', 'gt.id=tg.calc_groupteacher');
		}
	    // если задан фильтр по типу оформления, подключаем таблицу calc_statusjob
	    //if($statusjob||Yii::$app->session->get('user.ustatus')==3){
        //    $teachers = $teachers->leftJoin('calc_statusjob cstj','cstj.id=tch.calc_statusjob');
	    //}
	    // если задан фильтр по языку, подключаем табличку calc_langteacher
        if($params['TLID']){
            $teachers = $teachers->leftJoin('calc_langteacher clt', 'tch.id=clt.calc_teacher');
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 10) {
            $teachers = $teachers->innerJoin('calc_edunormteacher ent', 'tch.id=ent.calc_teacher');
        }
		// добавляем условие на выборку действующих преподавателей
        $teachers = $teachers
        ->where('tch.visible=:vis',[':vis'=>1])
        ->andFilterWhere(['tch.old'=>$params['STATE']]);
		// если задана переменная с id офиса
		if($params['TOID'] && $params['TOID'] !== 'all'){
			// добавляем условие выборки по id офиса
			$teachers = $teachers->andWhere(['gt.visible' => 1]);
			$teachers = $teachers->andFilterWhere(['gt.calc_office' => $params['TOID']]);
		}
		// если задан фильтр по типу оформления, добавляем условие выборки
        if($params['TJID']){
            $teachers = $teachers->andFilterWhere(['tch.calc_statusjob' => $params['TJID']]);
        }
		// если задан фильтр по месту работы, добавляем условие выборки
        // if($params['JPID']){
        if ((int)Yii::$app->session->get('user.ustatus') === 10) {
            $teachers = $teachers->andFilterWhere(['ent.company' => 2]);
            $teachers = $teachers->andFilterWhere(['ent.active' => 1]);
        }
        // }
        // если задан фильтр по языку, добавляем условие выборки
        if($params['TLID']){
            $teachers = $teachers->andFilterWhere(['clt.calc_lang' => $params['TLID']]);
        }

        // если задан поиск по тексту, добавляем условие выборки
        if($params['TSS']){
            $teachers = $teachers->andFilterWhere(['like', 'tch.name', $params['TSS']]);
        }

        // делаем клон запроса
        $countQuery = clone $teachers;
        // получаем данные для паджинации
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        // добавляем условия сортировки
        $teachers = $teachers->orderby($sorting);

		// отрабатываем запрос с с ограничениями на колич строк
		if(Yii::$app->request->get('page')&&Yii::$app->request->get('page') > 0){
		  $limit = 20;
		  $offset = 20 * (Yii::$app->request->get('page') - 1);
		  $teachers = $teachers->limit($limit)->offset($offset)->all();
		}
		// по дефолту выводим 20 строк начиная с первой
		else{
			$teachers = $teachers->limit(20)->all();
		}
		
		// выбираем из базы список языков для селектов
		$teacherlangs =  (new \yii\db\Query())
		->select('cl.id as lid, cl.name as lname, clt.calc_teacher as tid')
		->from('calc_lang cl')
		->leftJoin('calc_langteacher clt', 'cl.id=clt.calc_lang')
		->where('clt.visible=:vis', [':vis'=>1])
		->orderBy(['cl.name'=>SORT_ASC,'clt.calc_teacher'=>SORT_ASC])
		->all();

		$teacheroffices = (new \yii\db\Query())
		->select('co.id as oid, co.name as oname, ct.id as tid')
		->from('calc_office co')
		->leftJoin('calc_groupteacher cgt','cgt.calc_office=co.id')
		->leftJoin('calc_teacher ct','ct.id=cgt.calc_teacher')
		->where('cgt.visible=:vis and co.visible=:vis', [':vis'=>1])
		->orderBy(['co.name'=>SORT_ASC])
		->all();
			
		// выбираем из базы список типов трудовых договоров для селекта
		$teacherjobstates = (new \yii\db\Query())
		->select('csj.id as fid, csj.name as fname, tch.id as tid')
		->from('calc_statusjob csj')
		->leftJoin('calc_teacher tch','csj.id=tch.calc_statusjob')
		->where('tch.visible=:vis',[':vis'=>1])
		->orderBy(['csj.name'=>SORT_ASC])
		->all();

		// выбираем из базы ставки преподавателей
		$teachertax = (new \Yii\db\Query())
		->select('cent.calc_teacher as tid, cen.value as taxname, cent.company as tjplace')
		->from('calc_edunormteacher cent')
		->leftJoin('calc_edunorm cen','cen.id=cent.calc_edunorm')
		->where('cent.visible=:vis and cent.active=:vis',[':vis'=>1])
		->all();
		
		$i=0;
		// объявляем новый массив
		$teachersids = [];
		// распечатываем массив преподавателей
		foreach($teachers as $teacher){
			// заполняем массив
			$teachersids[$i] = $teacher['tid'];
			$i++;
		}
		
		// задаем массив со списком групп преподавателей
		$groups = [];
		// задаем массив с колич учеников 
		$pupils = [];
        // задаем массив занятий ожидающих проверки
		$unviewedlessons = [];
		if(!empty($teachersids)){
			
			// формируем подзапрос для выборки количество учеников в группах
			$SubQuery2 = (new \yii\db\Query())
			->select('count(csg2.calc_studname) as pcount')
			->from('calc_groupteacher cgt2')
			->leftJoin('calc_studgroup csg2','csg2.calc_groupteacher=cgt2.id')
			->where('csg2.visible=:visible and cgt2.id=tg.calc_groupteacher',[':visible'=>1]);
			
			// выбираем группы для ранее полученного списка преподавателей
			$groups = (new \yii\db\Query())
			->select('gt.id as gid, cs.id as sid, tg.calc_teacher as tid, cs.name as sname, co.name as oname, ce.name as ename, gt.data as gdate')
			->addSelect(['pupil'=>$SubQuery2])
			->from('calc_teachergroup tg')
			->leftJoin('calc_groupteacher gt', 'gt.id=tg.calc_groupteacher')
			->leftjoin('calc_service cs', 'cs.id=gt.calc_service')
			->leftjoin('calc_office co', 'co.id=gt.calc_office')
			->leftjoin('calc_edulevel ce', 'ce.id=gt.calc_edulevel')
			->leftjoin('calc_lang cl', 'cl.id=cs.calc_lang')
			->where('gt.visible=:vis and tg.visible=:vis', [':vis' => 1])
			->andWhere(['in','tg.calc_teacher',$teachersids])
			->all();
		    // уничтожаем переменнную
			unset($SubQuery2);

			// выбираем колмч занятий для проверки по преподавателям
			$unviewedlessons = (new \yii\db\Query())
			->select('jg.calc_teacher as tid, count(jg.id) as lcount')
			->from('calc_journalgroup jg')
			->innerJoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
			->where('jg.visible=:vis and jg.view=:view and jg.user!=:zero', [':vis'=>1, ':view'=>0, ':zero'=>0])
			->andWhere(['in', 'jg.calc_teacher', $teachersids]);
            if ((int)Yii::$app->session->get('user.ustatus') === 4) {
				$unviewedlessons = $unviewedlessons->andWhere(['gt.calc_office' => (int)Yii::$app->session->get('user.uoffice_id')]);

			}
			$unviewedlessons = $unviewedlessons->groupby(['jg.calc_teacher'])->all();
		}

        // выводим полученные данные в представление
        return $this->render('index', [
            'teachers'=>$teachers,
            'pages' => $pages,
			'teacheroffices'=>$teacheroffices,
            'teacherlangs'=>$teacherlangs,
            'teacherjobstates'=>$teacherjobstates,
            'teachertax'=>$teachertax,
	        'groups' => $groups,
	        'unviewedlessons' => $unviewedlessons,
			'params' => $params,
			'userInfoBlock' => User::getUserInfoBlock(),
			'jobPlace' => [ 1 => 'ШИЯ', 2 => 'СРР' ]
		]);
    }

    /**
     * Displays a single CalcTeacher model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        // проверяем какие данные выводить в карочку преподавателя: 1 - активные группы, 2 - завершенные группы, 3 - начисления; 4 - выплаты фонда
        if(Yii::$app->request->get('tab')) {
            // если вкладка задана, присваиваем в переменную
            $tab = Yii::$app->request->get('tab');
        } else {
            // для бухгалтера по умолчанию вкладка начисления
            if(Yii::$app->session->get('user.ustatus')==8) {
                $tab = 3;
            } else {
                // если нет, по умолчанию выводится информация по активным группам
                $tab = 1;
            }	
        }
        // по умолчанию год не задан
        $year=NULL;

        // выбираем из базы ставки
        $teachertax = (new \Yii\db\Query())
        ->select('cen.name as taxname, cen.value as taxvalue, cent.company as tjplace, cent.data as taxdate')
        ->from('calc_edunormteacher cent')
        ->leftJoin('calc_edunorm cen','cen.id=cent.calc_edunorm')
        ->where('cent.visible=:vis and cent.calc_teacher=:id and cent.active=:vis',[':vis'=>1, ':id'=>$id])
        ->orderby(['cent.data'=>SORT_DESC])
        ->all();

		// выбираем расписание преподавателя
		$teacherschedule = (new \Yii\db\Query())
		->select('csch.id as schid, csch.calc_denned as day, csch.time_begin as time_begin, cgt.id as gid, csch.time_end as time_end, cs.name as service, co.name as office, cco.name as room, el.name as level')
		->from('calc_schedule csch')
		->leftJoin('calc_groupteacher cgt' , 'cgt.id=csch.calc_groupteacher')
		->leftJoin('calc_service cs', 'cs.id=cgt.calc_service')
		->leftJoin('calc_office co', 'co.id=csch.calc_office')
		->leftJoin('calc_cabinetoffice cco', 'cco.id=csch.calc_cabinetoffice')
		->leftJoin('calc_edulevel el', 'el.id=cgt.calc_edulevel')
		->where('csch.calc_teacher=:id and csch.visible=:vis and csch.calc_groupteacher!=:gid', [':id'=>$id, ':vis'=>1, ':gid'=>0])
		->all();
		
		$i = 0;
		foreach($teacherschedule as $ts){
			// запрашиваем количество учеников в группе
			$pcount = (new \yii\db\Query())
			->select('count(id) as cnt')
			->from('calc_studgroup')
			->where('visible=:vis and calc_groupteacher=:gid', [':vis'=>1, ':gid'=>$ts['gid']])
			->one();
			// добавляем колич учеников в исходный массив
			$teacherschedule[$i]['cnt'] = $pcount['cnt'];
			$i++;
		}
		
		// делаем подзапрос на выборку колич посетивших занятие
		$subQuery1 = (new \yii\db\Query())
		->select('count(sjg.id)')
		->from('calc_studjournalgroup sjg')
		->where('sjg.calc_statusjournal=:present and sjg.calc_journalgroup=jg.id');
		// делаем подзапрос на выборку колич студентов в группе
		$subQuery2 = (new \yii\db\Query())
		->select('count(sg.id)')
		->from('calc_studgroup sg')
		->where('sg.visible=:vis and sg.calc_groupteacher=gt.id');
		// выбираем 5 занятий для проверки
		$unviewedlessons = (new \yii\db\Query())
		->select(['lesdate'=>'jg.data', 'jid'=>'jg.id', 'gid'=>'gt.id', 'office'=>'o.name', 'present'=>$subQuery1, 'all'=>$subQuery2])
		->from('calc_journalgroup jg')
		->leftJoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
		->leftJoin('calc_office o', 'gt.calc_office=o.id')
		->where('jg.visible=:vis and jg.view=:view and jg.calc_teacher=:id and jg.user!=:zero', [':vis'=>1, ':view'=>0, ':id'=>$id, ':zero'=>0, ':present'=>1]);
		if ((int)Yii::$app->session->get('user.ustatus') === 4) {
			$unviewedlessons = $unviewedlessons->andWhere(['gt.calc_office' => (int)Yii::$app->session->get('user.uoffice_id')]);

		}
		$unviewedlessons = $unviewedlessons->orderby(['jg.data'=>SORT_DESC])->limit(5)->all();
		unset($subQuery1);
		unset($subQuery2);

		// формируем массив с колич групп разных типов
        $efm = ['individual'=>0, 'group'=>0, 'minigroup'=>0, 'other'=>0];
		
		// выбираем подробную информацию по группам
		if($tab == 1 || $tab == 2) {
			switch($tab){
				case 1: $active = 1; $year = NULL; break;
				case 2: $active = 0; break;
				default: $active = 1; $year = NULL;
			}        

			//формируем подзапрос для выборки данных о ожидающих проверки занятиях
			$subQuery1 = (new \yii\db\Query())
			->select('count(id)')
			->from('calc_journalgroup')
			->where('visible=:vis and calc_teacher=:tid and calc_groupteacher=cgt.id and view=:zero and user!=:zero', [':vis'=>1, ':tid'=>$id, ':zero'=>0]);

			// формируем подзапрос для выборки часов к начислению
			$subQuery2 = (new \yii\db\Query())
			->select('sum(tn1.value)')
			->from('calc_journalgroup jg1')
			->leftJoin('calc_groupteacher gt1', 'gt1.id=jg1.calc_groupteacher')
			->leftJoin('calc_service s1', 's1.id=gt1.calc_service')
			->leftJoin('calc_timenorm tn1', 'tn1.id=s1.calc_timenorm')
			->where('jg1.visible=:vis and jg1.calc_teacher=:tid and jg1.calc_groupteacher=cgt.id and jg1.view=:vis and jg1.done=:done', [':vis'=>1, ':tid'=>$id, ':done'=>0]);
        
			// формируем подзапрос для выборки проведенных часов
			$subQuery3 = (new \yii\db\Query())
			->select('sum(tn2.value)')
			->from('calc_journalgroup jg2')
			->leftJoin('calc_groupteacher gt2', 'gt2.id=jg2.calc_groupteacher')
			->leftJoin('calc_service s2', 's2.id=gt2.calc_service')
			->leftJoin('calc_timenorm tn2', 'tn2.id=s2.calc_timenorm')
			->where('jg2.visible=:vis and jg2.calc_teacher=:tid and jg2.calc_groupteacher=cgt.id and jg2.view=:vis', [':vis'=>1, ':tid'=>$id]);

			// выбираем данные по группам из базы
			$teacherdata = (new \Yii\db\Query())
			->select(['gid'=>'cgt.id', 'visible'=>'cgt.visible', 'level'=>'cel.name', 'service'=>'cs.name', 'sid'=>'cs.id', 'eduform'=>'cs.calc_eduform', 'office'=>'co.name', 'start_date'=>'cgt.data', 'creator'=>'u.name', 'duration'=>'ctn.value', 'corp' => 'cgt.corp', 'direction' => 'cgt.company','ltch'=>$subQuery1, 'htacc'=>$subQuery2, 'vless'=>$subQuery3])
			->from('calc_teachergroup ctg')
			->leftJoin('calc_groupteacher cgt', 'ctg.calc_groupteacher=cgt.id')
		    ->leftJoin('calc_edulevel cel', 'cel.id=cgt.calc_edulevel')
			->leftJoin('calc_service cs', 'cs.id=cgt.calc_service')
			->leftJoin('calc_office co', 'co.id=cgt.calc_office')
			->leftJoin('user u', 'u.id=cgt.user')
			->leftJoin('calc_timenorm ctn', 'ctn.id=cs.calc_timenorm');
            if ((int)$active === 1) {
			    $teacherdata = $teacherdata->where('ctg.calc_teacher=:id and cgt.visible=:one and ctg.visible=:one', [':id' => $id, ':one' => 1]);
			} else {
                $teacherdata = $teacherdata->where('ctg.calc_teacher=:id and (cgt.visible=:zero or (cgt.visible=:one and ctg.visible=:zero))', [':id'=>$id, ':zero'=>0, ':one'=>1]);
            }
            $teacherdata = $teacherdata->andFilterWhere(['year(cgt.data)'=>$year])
			->orderby(['cgt.data'=>SORT_DESC])
			->all();
			unset($subQuery1);
			unset($subQuery2);
			unset($subQuery3);
			

			// если список групп не пустой
			if(!empty($teacherdata)){
				$i = 0;
				foreach($teacherdata as $gr){
					// считаем типы групп
					switch($gr['eduform']){
						case 1: $efm['individual']+=1; break;
						case 2: $efm['group']+=1; break;
						case 3: $efm['minigroup']+=1; break;
						case 4: $efm['other']+=1; break;
					}
					
					// выбираем число студентов в группе
					$sarr = (new \yii\db\Query())
					->select(['stid'=>'sn.id','sname'=>'sn.name', 'visible'=>'sg.visible'])
					->from('calc_studgroup sg')
					->leftJoin('calc_studname sn', 'sg.calc_studname=sn.id')
					->where('sg.calc_groupteacher=:gid', [':gid'=>$gr['gid']])
					->all();
					// записываем число непрвоереренных занятий в исходный массив
					$teacherdata[$i]['sarr'] = $sarr;
					$i++;
				}           
			}
		}
		// выбираем подробную информацию по начислениям
		if($tab == 3) {
			// пишем подзапрос для выборки доп данных о начислениях
			$subQuery = (new \Yii\db\Query())
			->select('sum(ctn.value)')
			->from('calc_journalgroup cjg')
			->leftJoin('calc_groupteacher cgt', 'cgt.id=cjg.calc_groupteacher')
			->leftJoin('calc_service cs', 'cgt.calc_service=cs.id')
			->leftJoin('calc_timenorm ctn', 'ctn.id=cs.calc_timenorm')
			->where('cjg.calc_accrual=cat.id');
			// выбираем данные из базы
			$teacherdata = (new \Yii\db\Query())
			->select([
				'aid'          => 'cat.id',
				'date'         => 'cat.data',
				'gid'          => 'cat.calc_groupteacher',
				'groupCompany' => 'gt.company',
				'serviceName'  => 's.name',
				'tax'          => 'cen.value',
				'value'        => 'cat.value',
				'creator'      => 'u1.name',
				'create_date'  => 'cat.data',
				'done'         => 'cat.done',
				'finisher'     => 'u2.name',
				'finish_date'  => 'cat.data_done',
				'hours'        => $subQuery
			])
			->from('calc_accrualteacher cat')
			->leftJoin(['gt' => Groupteacher::tableName()], 'gt.id = cat.calc_groupteacher')
			->leftJoin(['s' => Service::tableName()], 's.id = gt.calc_service')
			->leftJoin('calc_edunormteacher cent', 'cat.calc_edunormteacher=cent.id')
			->leftJoin('calc_edunorm cen', 'cen.id=cent.calc_edunorm')
			->leftJoin('user u1', 'u1.id=cat.user')
			->leftJoin('user u2', 'u2.id=cat.user_done')
			->where('cat.visible=:vis and cat.calc_teacher=:id', [':vis'=>1, ':id'=>$id])
			->andFilterWhere(['year(cat.data)'=>$year])
			->orderby(['cat.data'=>SORT_DESC])
			->all();
			unset($subQuery);
		}
		// выбираем подробную информацию по отчислениям в фонд
		if($tab == 4) {
			$teacherdata = [];
		}
		// выбираем базовые данные по преподавателю
		$model = $this->findModel($id);

		// выбираем колич занятий на проверке
		$lestocheck = (new \yii\db\Query())
		->select('count(id) as cnt')
		->from('calc_journalgroup')
		->where('visible=:vis and calc_teacher=:teacher and view=:view and user!=:zero', [':vis'=>1, ':teacher'=>$id, ':view'=>0, ':zero'=>0])
		->one();

		// выбираем колич часов доступных для начисления
		$hourstoaccrual = (new \yii\db\Query())
		->select('sum(tn.value) as sm')
		->from('calc_journalgroup jg')
		->leftJoin('calc_groupteacher gt', 'gt.id=jg.calc_groupteacher')
		->leftJoin('calc_service s', 's.id=gt.calc_service')
		->leftJoin('calc_timenorm tn', 'tn.id=s.calc_timenorm')
		->where('jg.visible=:vis and jg.calc_teacher=:id and jg.view=:view and jg.done=:done', [':vis'=>1, ':id'=>$id, ':view'=>1, ':done'=>0])
		->one();

		// выбираем сумму средств доступных для выплаты
		$sum2pay = (new \yii\db\Query())
		->select('sum(value) as money')
		->from('calc_accrualteacher')
		->where('calc_teacher=:tid and done!=:one and visible=:one', [':tid'=>$id, ':one'=>1])
		->one();

		$accrual = AccrualTeacher::calculateFullTeacherAccrual((int)$id);

		return $this->render('view', [
			'model'           => $model,
			'teachertax'      => $teachertax,
			'teacherschedule' => $teacherschedule,
			'viewedLessons'   => $accrual['lessons'] ?? [],
			'teacherdata'     => $teacherdata,
			'lestocheck'      => $lestocheck,
			'hourstoaccrual'  => $hourstoaccrual,
			'unviewedlessons' => $unviewedlessons,
			'efm'             => $efm,
			'accrualSum'      => $accrual['totalValue'],
			'sum2pay'         => $sum2pay,
			'userInfoBlock'   => User::getUserInfoBlock(),
			'jobPlace' => [ 1 => 'ШИЯ', 2 => 'СРР' ]
		]);
    }

    /**
     * Creates a new Teacher model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // всех кроме руководителей перенаправляем обратно
        if((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.uid') !== 296) {
            return $this->redirect(['teacher/index']);
        } else {
            $model = new Teacher();

			//выбираем формы трудоустройства для селекта
			$statusjob = (new \yii\db\Query())
			->select('id, name')
			->from('calc_statusjob')
			->all();
	
			if ($model->load(Yii::$app->request->post())) {
				$model->name = trim($model->name);
				$model->social_link = trim($model->social_link);
				$model->social_link = str_replace("http://", "", $model->social_link);
				$model->social_link = str_replace("https://", "", $model->social_link);
				$model->visible = 1;
				if($model->save()) {
					if (array_key_exists('new',Yii::$app->request->post())) {
						$user = new User();
						$user->name = $model->name;
						$user->login = 'user-' . time();
						$user->pass = 'pass-' . time();
						$user->visible = 1;
						$user->site = 1;
						$user->pass = md5($user->pass);
						$user->status = '5';
						$user->calc_teacher = $model->id;
						$user->calc_office = 0;
						$user->calc_city = 0;
						if ($user->save()) {
							return $this->redirect(['teacher/view', 'id' => $model->id]);
						}
					}
				}
				return $this->redirect(['teacher/view', 'id' => $model->id]);
			} else {
				return $this->render('create', [
					'model' => $model,
				    'statusjob' => $statusjob,
				    'userInfoBlock' => User::getUserInfoBlock()
				]);
			}
		}
    }

    /**
     * Updates an existing CalcTeacher model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        // всех кроме руководителей перенаправляем обратно
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4 && (int)Yii::$app->session->get('user.uid') !== 296) {
            return $this->redirect(['teacher/view', 'id'=>$id]);
        } else {
            //выбираем формы трудоустройства для селекта
            $statusjob = (new \yii\db\Query())
            ->select('id, name')
            ->from('calc_statusjob')
            ->all();

            $model = $this->findModel($id);

            if($model->load(Yii::$app->request->post())) {
                	$model->name = trim($model->name);
                	$model->social_link = trim($model->social_link);
                	$model->social_link = str_replace("http://", "", $model->social_link);
                	$model->social_link = str_replace("https://", "", $model->social_link);
                if($model->save()) {
                	Yii::$app->session->setFlash('success', Yii::t('app','Информация о преподавателе успешно обновлена!'));
                } else {
                	Yii::$app->session->setFlash('error', Yii::t('app','Не удалось обновить информацию о преподавателе!'));
                }

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
		    		'statusjob' => $statusjob,
		    		'userInfoBlock' => User::getUserInfoBlock()
                ]);
            }
        }
    }

    /**
     * Deletes an existing CalcTeacher model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if((int)Yii::$app->session->get('user.ustatus') !== 3) {
            return $this->redirect(['teacher/view', 'id'=>$id]);
        } else {
            $teacher = $this->findModel($id);
            if ($teacher->visible !== 0) {
                $teacher->visible = 0;
                $teacher->save();
            }
            return $this->redirect(['index']);
        }
	}

    /**
     * @param $tid
     *
     * @return mixed
     */
	public function actionLanguagePremiums($tid)
	{
        $model = new TeacherLanguagePremium();
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                $t = Yii::$app->db->beginTransaction();
                try {
                    if (!TeacherLanguagePremium::removeDuplicateLanguagePremium($model->language_premium_id, $model->company, $tid)) {
                        throw new ServerErrorHttpException(Yii::t('app','Failed to add language premium to the teacher!'));
                    }
                    $model->teacher_id = $tid;
                    if (!$model->save()) {
                        throw new ServerErrorHttpException(Yii::t('app','Failed to add language premium to the teacher!'));
                    } else {
                        $t->commit();
                        Yii::$app->session->setFlash('success', Yii::t('app','Language premium successfully added to the teacher!'));
                        $this->redirect(['language-premiums', 'tid' => $tid]);
                    }
                } catch (\Exception $e) {
                    $t->rollBack();
                    Yii::$app->session->setFlash('error', Yii::t('app', $e->getMessage()));
                }
            }
        }

        return $this->render('language-premiums', [
            'model'            => $model,
            'teacher'          => Teacher::findOne($tid),
            'premiums'         => LanguagePremium::getLanguagePremiumsSimple(),
            'teacherPremiums'  => TeacherLanguagePremium::getTeacherLanguagePremiums($tid),
            'userInfoBlock'    => User::getUserInfoBlock(),
        ]);
	}

    /**
     * @param $id
     * @param $tid
     * @return mixed
     */
	public function actionDeleteLanguagePremium($id, $tid)
    {
        if (($model = TeacherLanguagePremium::findOne($id)) !== NULL) {
            $model->visible = 0;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app','Teacher language premium successfully removed!'));
            } else {
                Yii::$app->session->setFlash('success', Yii::t('app','Failed to remove teacher language premium!'));
            }
        }

        return $this->redirect(['teacher/language-premiums', 'tid' => $tid]);
    }

    /**
     * Finds the Teacher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Teacher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Teacher::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	protected function getUrlParams($params)
	{
		/* проверяем GET запрос на наличие переменной TOID (фильтр по офису) */
		if(Yii::$app->request->get('TOID')){
		    $params['TOID'] = Yii::$app->request->get('TOID');
	    } else {
			if ((int)Yii::$app->session->get('user.ustatus') === 4) {
				$params['TOID'] = (int)Yii::$app->session->get('user.uoffice_id');
			}
		}

		/* проверяем GET запрос на наличие переменной TJID (фильтр по типу договора) */
		if(Yii::$app->request->get('TJID')&&Yii::$app->request->get('TJID')!='all'){
		    $params['TJID'] = Yii::$app->request->get('TJID');
		}

		// /* проверяем GET запрос на наличие переменной TJID (фильтр по месту работы) */
		// if(Yii::$app->request->get('JPID')&&Yii::$app->request->get('JPID')!='all'){
		//     $params['JPID'] = Yii::$app->request->get('JPID');
		// }

		/* проверяем GET запрос на наличие переменной TLID (фильтр по языку) */
		if(Yii::$app->request->get('TLID')&&Yii::$app->request->get('TLID')!='all'){
		    $params['TLID'] = Yii::$app->request->get('TLID');
		}

		/* проверяем GET запрос на наличие переменной TSS (фильтр по имени) */
		if(Yii::$app->request->get('TSS')&&Yii::$app->request->get('TSS')!=''){
		    $params['TSS'] = Yii::$app->request->get('TSS');
		}

		/* проверяем GET запрос на наличие переменной STATE (фильтр по состоянию) */
		if(Yii::$app->request->get('STATE') || Yii::$app->request->get('STATE') == 0){
			// если нужно вывести все записи
			if(Yii::$app->request->get('STATE') == 'all'){
				// выставляем переменную в NULL
			    $params['STATE'] = NULL;	
			} else {
		        switch(Yii::$app->request->get('STATE')){
				    // преподаватель со статусом "С НАМИ"
				    case 0: $params['STATE'] = 0; break;
				    // преподаватель со статусом "НЕ С НАМИ"
				    case 1: $params['STATE'] = 1; break;
                    // преподаватель со статусом "В отпуске"
					case 2: $params['STATE'] = 2; break;
					// преподаватель со статусом "В декрете"
					case 3: $params['STATE'] = 3; break;
					// по умолчанию 0 (С НАМИ)
                    default: $params['STATE'] = 0;				
			    }
			}
		}

		return $params;
	}
}
