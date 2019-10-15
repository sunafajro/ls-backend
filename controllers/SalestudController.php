<?php

namespace app\controllers;

use app\models\Salestud;
use app\models\search\StudentDiscountSearch;
use app\models\Student;
use app\models\User;
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
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['approve', 'autocomplete', 'create', 'disable', 'disable-all', 'enable', 'index'],
                'rules' => [
                    [
                        'actions' => ['approve', 'autocomplete', 'create', 'disable', 'disable-all', 'enable', 'index'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['approve', 'autocomplete', 'create', 'disable', 'disable-all', 'enable', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'approve' => ['post'],
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
     * Links sale to the student
     * @return mixed
     */
    public function actionCreate($sid)
    {
        $model = new Salestud();
        $student = Student::findOne($sid);
        $sales = $student->getStudentSales();

        // если данные пришли и успешно залились в модель
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->calc_studname    = $sid;
            $model->user             = Yii::$app->session->get('user.uid');
            $model->data             = date('Y-m-d');
            $model->visible          = 1;
            $model->data_visible     = '0000-00-00';
            $model->user_visible     = 0;
            $model->data_used        = '0000-00-00';
            $model->user_used        = 0;
            /* если скидка назначается руководителем, сразу подтверждаем */
            if ((int)Yii::$app->session->get('user.ustatus') === 3) {
                $model->approved     = 1;
            } else {
                $model->approved     = 0;
            }
            if (!Salestud::find()->where(['calc_studname' => $sid, 'calc_sale' => $model->calc_sale])->exists()) {
                if($model->save()) {
                    Yii::$app->session->setFlash('success', 'Скидка успешно добавлена!');
                    return $this->redirect(['salestud/create', 'sid' => $sid]);
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось добавить скидку!');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Скидка уже назначена!');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'sales' => $sales,
            'student' => $student,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }
    
    /**
     * Approves client discount
     * @return mixed
     */
    public function actionApprove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $id = (int)Yii::$app->request->post('id', NULL);
        $status = Yii::$app->request->post('status', 'approve');
        if ($id && $status) {
            $sale = Salestud::findOne($id);
            if (empty($sale)) {
                throw new NotFoundHttpException(Yii::t('yii', 'The requested page does not exist.'));
            }
            if ($status === 'accept') {                
                $sale->approved = 1;
                if (!$sale->save()) {
                    Yii::$app->session->setFlash('error', 'Не удалось поддтвердить скидку.');
                }
            } else if ($status === 'refuse') {
                if (!$sale->delete()) {
                    Yii::$app->session->setFlash('error', 'Не удалось аннулировать скидку.');
                }
            } else {
                throw new BadRequestHttpException(Yii::t('yii', 'Missing required arguments: { status }'));
            }
            return $this->redirect(Yii::$app->request->referrer);        
        } else {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required arguments: { id }'));
        }
    }

    /**
     * Restores client discount
     * @return mixed
     */
    public function actionEnable($id)
    {
        $model = $this->findModel($id);
        if((int)$model->visible !== 1) {
            $model->visible = 1;
            $model->user_visible = Yii::$app->session->get('user.uid');
            $model->data_visible = date('Y-m-d');
            if ((int)Yii::$app->session->get('user.ustatus') === 3) {
                $model->approved = 1;
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Скидка успешно восстановлена!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось восстановить скидку!');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Removes client discount
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
     * Removes all client discounts
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

    public function actionAutocomplete(string $sid)
    {
        $student = Student::findOne($sid);
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
