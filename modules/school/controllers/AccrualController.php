<?php

namespace app\modules\school\controllers;

use app\models\Journalgroup;
use app\models\Teacher;
use app\modules\school\models\AccrualTeacher;
use app\modules\school\models\Auth;
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
 * Class AccrualController
 * @package app\modules\school\controllers
 */
class AccrualController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors() : array
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
     * @param string      $tid   id преподавателя
     * @param string|null $month месяц в котором прошли занятия
     * @param string|null $year  год в котором прошли занятия
     *
     * @return mixed
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @uses $_POST['groups'] directly
     */
    public function actionCreate(string $tid, string $month = NULL, string $year = NULL)
    {
        if (is_string($tid)) {
            $tid = (int)$tid;
        }
        if (!is_null($month) && is_string($month)) {
            $month = (int)$month;
        }
        if (!is_null($year) && is_string($year)) {
            $year = (int)$year;
        }
        /** @var Auth $user */
        $user = Yii::$app->user->identity;
        if (!in_array($user->roleId, [3])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
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
				$totalAccrual = AccrualTeacher::calculateFullTeacherAccrual($tid, $gid, $month, $year);
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
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException|\Throwable
     */
	public function actionDelete(string $id)
	{
        if (is_string($id)) {
            /** @var int $id */
            $id = (int)$id;
        }

        /** @var Auth $user */
        $user = Yii::$app->user->identity;
        if (!in_array($user->roleId, [3])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $model = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();
        if ($model->delete()) {
            $transaction->commit();
            Yii::$app->session->setFlash('success', "Начисление #{$id} успешно отменено!");
        } else {
            $transaction->rollback();
            Yii::$app->session->setFlash('error', "Неудалось отменить начисление #{$id}!");
        }

		return $this->redirect(['teacher/view', 'id' => $model->calc_teacher, 'tab' => 3]);
	}

    /**
     * Ставить на начисления отметку "Выплаченное"
     * @param string|null $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     * @uses $_POST['accruals'] directly
     */
	public function actionDone(string $id = null)
	{
        if (is_string($id)) {
            /** @var int $id */
            $id = (int)$id;
        }

        /** @var Auth $user */
        $user = Yii::$app->user->identity;
        if (!in_array($user->roleId, [3, 8])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $models = [];
        if ($id) {
            $model = $this->findModel($id);
            $models[] = $model;
        } else {
            $accruals = explode(',', Yii::$app->request->post('accruals', ''));
            if (empty($accruals)) {
                throw new BadRequestHttpException('Missing POST param: $accruals.');
            }
            $models = AccrualTeacher::find()->andWhere(['id' => $accruals])->all();
        }

        $result = [
            'success' => [],
            'error' => [],
        ];
        foreach ($models as $model) {
            if ($model->payoff()) {
                $result['success'][] = $model->id;
            } else {
                $result['error'][] = $model->id;
            }
        }

        if (!empty($result['success'])) {
            Yii::$app->session->setFlash('success', 'Начисления успешно выплачены: #' . join(', ', $result['success']) . '!');
        }
        if (!empty($result['error'])) {
            Yii::$app->session->setFlash('error', 'Неудалось выплатить начисления: #' . join(', ', $result['error']) . '!');
        }

        return $this->redirect(Yii::$app->request->referrer);
	}

    /**
     * Помечает начисление как "Ожидает выплаты"
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
	public function actionUndone(string $id)
	{
	    if (is_string($id)) {
	        /** @var int $id */
	        $id = (int)$id;
        }
        /** @var Auth $user */
        $user = Yii::$app->user->identity;
        if (!in_array($user->roleId, [3, 8])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $model = $this->findModel($id);

        if ($model->payoffReset()) {
            Yii::$app->session->setFlash('success', "Выплата начисления #$id успешно отменена!");
        } else {
            Yii::$app->session->setFlash('error', "Неудалось отменить выплатиту начисления #$id!");
        }

        return $this->redirect(Yii::$app->request->referrer);
	}
	
	/**
     * Finds the AccrualTeacher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     *
     * @return AccrualTeacher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id) : AccrualTeacher
    {
        if (($model = AccrualTeacher::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
