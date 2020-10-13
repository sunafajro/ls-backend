<?php

namespace app\modules\school\controllers;

use app\models\AccrualTeacher;
use app\models\Journalgroup;
use app\models\Teacher;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;

/**
 * AccrualController используем для записи начислений зп в базу.
 */
class AccrualController extends Controller
{
    public function behaviors()
    {
		$rules = ['create', 'delete', 'done', 'undone'];
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
                    'create' => ['post'],
                    'delete' => ['post'],
                    'done'   => ['post'],
                    'undone' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Добавляет начисление
     * @param int      $tid   id преподавателя
     * @param int|null $month месяц в котором прошли занятия
     *
     * @return mixed
     *
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @uses $_POST['groups'] directly
     */
    public function actionCreate(int $tid, int $month = null)
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3) {
            throw new ForbiddenHttpException();
        }
        /** @var Teacher $teacher */
		$teacher = Teacher::find()->andWhere(['id' => $tid])->one();
        if (empty($teacher)) {
            throw new NotFoundHttpException("Преподаватель #${tid} не найден.");
        }
		$groups = explode(',', Yii::$app->request->post('groups', ''));
		if (empty($groups)) {
            throw new BadRequestHttpException('Missing POST param: $groups.');
		}

		$transaction = Yii::$app->db->beginTransaction();
		try {
		    foreach ($groups as $gid) {
				/** @var array */
				$totalAccrual = AccrualTeacher::calculateFullTeacherAccrual((int)$tid, (int)$gid, $month);
				$lessons = [];
				foreach ($totalAccrual['lessons'] ?? [] as $lesson) {
                    $lessons[$lesson['id']] = [
                        'corpPremium'      => $lesson['corpPremium'],
                        'dayTimeMarkup'    => $lesson['dayTimeMarkup'],
                        'groupLevelRate'   => $lesson['groupLevelRate'],
                        'hoursCount'       => (float)$lesson['time'],
                        'languagePremium'  => $lesson['languagePremium'],
                        'studentCountRate' => $lesson['studentCountRate'],
                        'wageRate'         => $lesson['wageRate'],
                        'totalValue'       => $lesson['totalValue'],
                    ];
                }
				// заливаем данные о начислении в модель
				$model                      = new AccrualTeacher();
				$model->calc_groupteacher   = $gid;
				$model->calc_teacher        = $tid;
				$model->value               = $totalAccrual['totalValue'];
				$model->value_corp          = $totalAccrual['corpPremium'];
				$model->value_prem          = $totalAccrual['languagePremium'];
				$model->calc_edunormteacher = $totalAccrual['wageRateId'];
				$model->outlay              = $lessons;
	
				if ($model->save()) {
					// получаем сумму начислений преподавателя
                    $teacher->accrual = $totalAccrual['totalValue'] + $teacher->accrual;
                    // обновляем сумму начислений у преподавателя
					if (!$teacher->save(true, ['accrual'])) {
                        throw new ServerErrorHttpException('Не удалось создать начисление. Ошибка обновления преподавателя.');
                    }
                    // обновляем записи в журнале
					/** @var Journalgroup $lesson */
                    foreach (Journalgroup::find()->andWhere(['in', 'id', array_keys($lessons)])->all() ?? [] as $lesson) {
                        $lesson->done         = 1;
                        $lesson->user_done    = Yii::$app->session->get('user.uid');
                        $lesson->data_done    = date('Y-m-d');
                        $lesson->calc_accrual = $model->id;
                        if (!$lesson->save(true, ['done', 'data_done', 'user_done', 'calc_accrual'])) {
                            throw new ServerErrorHttpException('Не удалось создать начисление. Ошибка обновления занятий.');
                        }
                    }
				} else {
					throw new ServerErrorHttpException('Не удалось создать начисление.');
				}
			}
			Yii::$app->session->setFlash('success', 'Начисление произведено успешно!');
			$transaction->commit();
		} catch (Exception $e) {
			Yii::$app->session->setFlash('error', 'Начисление произвести не удалось!');
			$transaction->rollback();
		}
		
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Аннулирует начисление
     * @param int $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
	public function actionDelete(int $id)
	{
        if ((int)Yii::$app->session->get('user.ustatus') !== 3) {
            throw new ForbiddenHttpException();
        }

        $model = $this->findModel($id);

		if ($model->visible) {
			if (!$model->done) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->visible = 0;
                    $model->data_visible = date('Y-m-d');
                    $model->user_visible = Yii::$app->session->get('user.uid');
                    if ($model->save(true, ['visible', 'data_visible', 'user_visible'])) {
                        /** @var Teacher $teacher */
                        $teacher = Teacher::find()->andWhere($model->calc_teacher)->one();
                        $teacher->accrual = $teacher->accrual - $model->value;
                        if (!$teacher->save(true, ['accrual'])) {
                            throw new ServerErrorHttpException('error', "Неудалось отменить начисление #$id!");
                        }
                        // обновляем записи в журнале
                        /** @var Journalgroup $lesson */
                        foreach (Journalgroup::find()->andWhere(['calc_accrual' => $id])->all() ?? [] as $lesson) {
                            $lesson->done         = 0;
                            $lesson->user_done    = 0;
                            $lesson->data_done    = '0000-00-00';
                            $lesson->calc_accrual = 0;
                            if (!$lesson->save(true, ['done', 'user_done', 'data_done', 'calc_accrual'])) {
                                throw new ServerErrorHttpException('Не удалось отменить начисление. Ошибка обновления занятий.');
                            }
                        }
                        // если отмена начисления начисление прошла не успешно
                        Yii::$app->session->setFlash('success', "Начисление #$id успешно отменено!");
                        $transaction->commit();
                    } else {
                        throw new ServerErrorHttpException('error', "Неудалось отменить начисление #$id!");
                    }
                } catch (Exception $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    $transaction->rollback();
                }
			} else {
				Yii::$app->session->setFlash('error', "Нельзя удалить начисление #$id, пожалуйста сначала отмените выплату!");
			}
		}
		
		return $this->redirect(['teacher/view', 'id' => $model->calc_teacher, 'tab' => 3]);
	}

    /**
     * Помечает начисление как "Выплаченное"
     * @param int         $id
     * @param string|null $type
     * @param int|null    $page
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
	public function actionDone(int $id, string $type = null, int $page = null)
	{
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 8])) {
            throw new ForbiddenHttpException();
        }

        $model = $this->findModel($id);

        if ($type === 'report') {
        	$tid = $model->calc_teacher;
			if ($page && (int)$page > 0) {
		        $tid = 'all';
		    }
            $backPath = ['report/accrual', 'page' => $page ?? 1, 'TID' => $tid,  '#' => 'block_tid_' . $model->calc_teacher];
        } else {
        	$backPath = ['teacher/view', 'id' => $model->calc_teacher, 'tab' => 3];
        }

		
		if ($model->visible) {
			if (!$model->done) {
				$model->done = 1;
				$model->user_done = Yii::$app->session->get('user.uid');
				$model->data_done = date('Y-m-d');
				if ($model->save(true, ['done', 'user_done', 'data_done'])) {
					// если начисление успешно выплачено
				    Yii::$app->session->setFlash('success', "Начисление #$id успешно выплачено!");
				} else {
					// если выплатить начисление не удалось
				    Yii::$app->session->setFlash('error', "Неудалось выплатить начисление #$id!");
				}
		    }
		}

		return $this->redirect($backPath);
	}

    /**
     * Помечает начисление как "Ожидает выплаты"
     * @param int $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
	public function actionUndone(int $id)
	{
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 8])) {
            throw new ForbiddenHttpException();
        }

        $model = $this->findModel($id);

		if ($model->visible) {
			if ($model->done) {
				$model->done = 0;
				$model->user_done = 0;
				$model->data_done = '0000-00-00';
				if ($model->save(true, ['done', 'user_done', 'data_done'])) {
				    Yii::$app->session->setFlash('success', "Выплата начисления #$id успешно отменена!");
				} else {
				    Yii::$app->session->setFlash('error', "Неудалось отменить выплатиту начисления #$id!");
				}
		    }
		}
		return $this->redirect(['teacher/view', 'id' => $model->calc_teacher, 'tab' => 3]);
	}
	
	/**
     * Finds the AccrualTeacher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     *
     * @return AccrualTeacher the loaded model
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
