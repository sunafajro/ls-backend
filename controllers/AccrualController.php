<?php

namespace app\controllers;

use Yii;
use app\models\AccrualTeacher;
use app\models\Teacher;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
//use yii\filters\VerbFilter;

/**
 * AccrualController используем для записи начислений зп в базу.
 */
class AccrualController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['addaccrual', 'delaccrual', 'doneaccrual', 'undoneaccrual'],
                'rules' => [
                    [
                        'actions' => ['addaccrual', 'delaccrual', 'doneaccrual', 'undoneaccrual'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['addaccrual', 'delaccrual', 'doneaccrual', 'undoneaccrual'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * производит начисление зп
     * @return mixed
     */
    public function actionAddaccrual($gid, $tid)
    {
        // всех кроме менеджеров и руководителей
        if(Yii::$app->session->get('user.ustatus')!=3) {
            // редиректим на главную
            $this->redirect(['report/index']);
        }

        $page = 1;
		
		if(Yii::$app->request->get('page') && (int)Yii::$app->request->get('page') > 0) {
		    $page = Yii::$app->request->get('page');
		}
		
        $calculate = AccrualTeacher::calculateFullTeacherAccrual($tid, $gid);

		// заливаем данные о начислении в модель
		$model = new AccrualTeacher();
		$model->calc_groupteacher = $gid;
		$model->calc_teacher = $tid;
		$model->value = $calculate['accrual'];
		$model->value_corp = $calculate['corp'];
		$model->value_prem = $calculate['prem'];
		$model->data = date('Y-m-d');
		$model->user = Yii::$app->session->get('user.uid');
		$model->calc_edunormteacher = $calculate['norm'];
		$model->visible = 1;		

		// если удалось записать начисление в базу то обновляем остальные таблицы
		if($model->save()){
			// получаем сумму начислений преподавателя
			$oldAccrual = (new \yii\db\Query())
			->select('accrual')
			->from('calc_teacher')
			->where('id=:id',[':id'=>$tid])
			->one();
			$accrual = $calculate['accrual'] + $oldAccrual['accrual'];
			// обновляем запись в журнале
			$db = (new \yii\db\Query())
			->createCommand()
			->update('calc_journalgroup',
			['done' => 1, 'user_done'=>Yii::$app->session->get('user.uid'), 'data_done'=>date('Y-m-d'), 'calc_accrual'=>$model->id],
			['in', 'id', $calculate['lids']])->execute();
			// обновляем сумму начислений у преподавателя
			$db = (new \yii\db\Query())
			->createCommand()
			->update('calc_teacher',['accrual' => $accrual],'id=:tid')
			->bindParam(':tid', $tid)
			->execute();		
		// если начисление прошло успешно
        Yii::$app->session->setFlash('success', 'Начисление произведено успешно!');
		} else {
			// если начисление прошло не успешно
			Yii::$app->session->setFlash('error', 'Начисление произвести неудалось!');
		}
		
		if(Yii::$app->request->get('page') && (int)Yii::$app->request->get('page') > 0) {
		    $tid = 'all';
		}
		
        return $this->redirect(['report/accrual', 'tid' => $tid, 'page' => $page, '#' => 'block_tid_' . $model->calc_teacher]);
    }
	
	/* аннулирует начисление */
	public function actionDelaccrual($id)
	{
        // всех руководителей
        if(Yii::$app->session->get('user.ustatus')!=3) {
            // редиректим на профиль преподавателя
            $this->redirect(['site/index']);
        }
		// находим запись по id
        $model = $this->findModel($id);
		if($model->visible) {
			if(!$model->done) {
				$model->visible = 0;
				$model->data_visible = date('Y-m-d');
				$model->user_visible = Yii::$app->session->get('user.uid');
				if($model->save()) {
					$teacher = Teacher::findOne($model->calc_teacher);
					$teacher->accrual = $teacher->accrual - $model->value;
					$teacher->save();
					// обновляем запись в журнале
					$db = (new \yii\db\Query())
					->createCommand()
					->update('calc_journalgroup', ['done'=>0, 'user_done'=>0, 'data_done'=>'0000-00-00', 'calc_accrual'=>0], ['calc_accrual'=>$id])
					->execute();
					// если отмена начисления начисление прошла не успешно
				    Yii::$app->session->setFlash('success', "Начисление #$id успешно отменено!");
				} else {
					// если отмена начисления начисление прошла не успешно
				    Yii::$app->session->setFlash('error', "Неудалось отменить начисление #$id!");
				}
			} else {
				// если начисление уже вылачено
				Yii::$app->session->setFlash('error', "Нельзя удалить начисление #$id, пожалуйста сначала отмените выплату!");
			}
		}
		
		$this->redirect(['teacher/view', 'id'=>$model->calc_teacher, 'tab'=>3]);
	}
	
	/* помечает начисление как "Выплаченное" */
	public function actionDoneaccrual($id)
	{
        /* всех кроме менеджеров и руководителей редиректим обратно */
        if(Yii::$app->session->get('user.ustatus')!=3 && Yii::$app->session->get('user.ustatus')!=8) {
            return $this->redirect(Yii::$app->request->referrer);
        }
		/* всех кроме менеджеров и руководителей редиректим обратно */
        $page = 1;
        // находим запись по id
        $model = $this->findModel($id);

        if(Yii::$app->request->get('type')=='report') {
        	$tid = $model->calc_teacher;
			if(Yii::$app->request->get('page') && (int)Yii::$app->request->get('page') > 0) {
		        $page = Yii::$app->request->get('page');
		        $tid = 'all';
		    }
        	$backpath = ['report/accrual', 'page' => $page, 'TID' => $tid,  '#' => 'block_tid_' . $model->calc_teacher];
        } else {
        	$backpath = ['teacher/view', 'id'=>$model->calc_teacher, 'tab'=>3];
        }

		
		if($model->visible) {
			if(!$model->done) {
				$model->done = 1;
				$model->user_done = Yii::$app->session->get('user.uid');
				$model->data_done = date('Y-m-d');
				if($model->save()) {
					// если начисление успешно выплачено
				    Yii::$app->session->setFlash('success', "Начисление #$id успешно выплачено!");
				} else {
					// если выплатить начисление не удалось
				    Yii::$app->session->setFlash('error', "Неудалось выплатить начисление #$id!");
				}
		    }
		}

		$this->redirect($backpath);
	}

	/* помечает начисление как "Ожидает выплаты" */
	public function actionUndoneaccrual($id)
	{
        /* всех кроме менеджеров и руководителей редиректим обратно */
        if(Yii::$app->session->get('user.ustatus')!=3 && Yii::$app->session->get('user.ustatus')!=8) {
            return $this->redirect(Yii::$app->request->referrer);
        }
		/* всех кроме менеджеров и руководителей редиректим обратно */

		// находим запись по id
        $model = $this->findModel($id);
		if($model->visible) {
			if($model->done) {
				$model->done = 0;
				$model->user_done = 0;
				$model->data_done = '0000-00-00';
				if($model->save()) {
					// если выплата начисления успешно отменена
				    Yii::$app->session->setFlash('success', "Выплата начисления #$id успешно отменена!");
				} else {
					// если отменить выплату начисления не удалось
				    Yii::$app->session->setFlash('error', "Неудалось отменить выплатиту начисления #$id!");
				}
		    }
		}
		$this->redirect(['teacher/view', 'id'=>$model->calc_teacher, 'tab'=>3]);
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
        if (($model = AccrualTeacher::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
