<?php

namespace school\controllers;

use school\models\Salestud;
use school\models\searches\StudentDiscountSearch;
use school\models\Student;
use school\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * SalestudController implements the CRUD actions for Salestud model.
 */
class SalestudController extends Controller
{
    public function behaviors()
    {
        $rules = ['approve', 'autocomplete', 'create', 'disable', 'disable-all', 'enable', 'index'];
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
                    'approve'      => ['post'],
                    'disable'      => ['post'],
                    'enable'       => ['post'],
                    'autocomplete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
	{
		if(parent::beforeAction($action)) {
            if ((int)Yii::$app->session->get('user.uid') === 389 && in_array($action->id, ['approve', 'index'])) {
                return true;
            }
			if (User::checkAccess($action->controller->id, $action->id) == false) {
				throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
			}
			return true;
		} else {
			return false;
		}
    }
    
    public function actionIndex()
    {
        $searchModel = new StudentDiscountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }
    
    /**
     * Назначает скидку клиенту
     * @param int $sid
     * 
     * @return mixed
     */
    public function actionCreate($sid)
    {
        $student = Student::findOne($sid);
        if (empty($student)) {
            throw new NotFoundHttpException(Yii::t('yii', 'The requested page does not exist.'));
        }
        $model = new Salestud();
        $sales = $student->getStudentSales();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->calc_studname = $sid;
            if ((int)Yii::$app->session->get('user.ustatus') === 3) {
                $model->approved = 1;
            }
            /** @var Salestud $oldSale */
            $oldSale = Salestud::find()->where(['calc_studname' => $sid, 'calc_sale' => $model->calc_sale])->one();
            if (empty($oldSale)) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Скидка успешно добавлена!');
                    return $this->redirect(['salestud/create', 'sid' => $sid]);
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось добавить скидку!');
                }
            } else {
                if ($oldSale->visible) {
                    Yii::$app->session->setFlash('error', 'Скидка уже назначена!');
                } else {
                    if ($oldSale->restore($model->reason)) {
                        Yii::$app->session->setFlash('success', 'Скидка успешно добавлена!');
                        return $this->redirect(['salestud/create', 'sid' => $sid]);
                    } else {
                        Yii::$app->session->setFlash('error', 'Не удалось добавить скидку!');
                    }
                }
            }
        }

        return $this->render('create', [
            'model'         => $model,
            'sales'         => $sales,
            'student'       => $student,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }
    
    /**
     * Подтверждает назначение или отказывает в назначении скидки
     * @param int    $id
     * @param string $status
     * 
     * @return mixed
     */
    public function actionApprove($id, $status)
    {
        $discount = $this->findModel($id);
        if ($status === 'accept') {                
            if (!$discount->approve()) {
                Yii::$app->session->setFlash('error', 'Не удалось поддтвердить скидку.');
            }
        } else if ($status === 'refuse') {
            if (!$discount->delete()) {
                Yii::$app->session->setFlash('error', 'Не удалось аннулировать скидку.');
            }
        }
        return $this->redirect(Yii::$app->request->referrer);        
    }

    /**
     * @deprecated
     * Восстанавливает скидку клиента
     * @param int    $id
     * 
     * @return mixed
     */
    public function actionEnable($id)
    {
        $model = $this->findModel($id);
        if((int)$model->visible !== 1) {
            if ($model->restore()) {
                Yii::$app->session->setFlash('success', 'Скидка успешно восстановлена!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось восстановить скидку!');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Аннулирует скидку клиента
     * @param int $id
     * 
     * @return mixed
     */
    public function actionDisable($id)
    {
        $discount = $this->findModel($id);
        if ($discount->delete()) {
            Yii::$app->session->setFlash('success', 'Скидка успешно аннулирована!');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось аннулировать скидку!');
        }
        
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Аннулирует скидку для всех клиентов
     * @param int $sid
     * 
     * @return mixed
     */
    public function actionDisableAll($sid)
    {
        $sql = (new \yii\db\Query())
        ->createCommand()
        ->update('calc_salestud', ['visible' => 0, 'user_visible' => Yii::$app->session->get('user.uid'), 'data_visible' => date('Y-m-d')], ['calc_sale' => $sid, 'visible' => 1])
        ->execute();
		
		if($sql > 0) {
			Yii::$app->session->setFlash('success', 'Скидка успешно аннулирована!');
		} else {
			Yii::$app->session->setFlash('error', 'Не удалось аннулировать скидку!');
		}
		
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $sid
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAutocomplete(string $sid)
    {
        $student = Student::findOne($sid);
        if (empty($student)) {
            throw new NotFoundHttpException(Yii::t('yii', 'The requested page does not exist.'));
        }
        $sales = $student->getStudentAvailabelSales(['term' => Yii::$app->request->post('term') ?? NULL]);
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $sales;
    }
    
    /**
     * Finds the Salestud model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Salestud the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Salestud::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
