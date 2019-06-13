<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Salestud;
use app\models\Student;
use app\models\User;
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
                'only' => ['create','disable','enable'],
                'rules' => [
                    [
                        'actions' => ['create', 'disable', 'enable', 'approve', 'disableall', 'autocomplete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','disable','enable', 'approve','disableall', 'autocomplete'],
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
			if (User::checkAccess($action->controller->id, $action->id) == false) {
				throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
			}
			return true;
		} else {
			return false;
		}
	}
    
    /**
     * Метод позволяет менеджеру или руководителю назначить студенту скидку. 
     * Для назначения необходим ID клиента.
     * Одному клиенту может быть назначено несколько активных скидок.
    **/
    public function actionCreate($sid)
    {
        $userInfoBlock = User::getUserInfoBlock();
        // создаем пустую запись
        $model = new Salestud();
        // находим данные по клиенту
        $student = Student::findOne($sid);
        // получаем список доступных скидок
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
            'userInfoBlock' => $userInfoBlock
        ]);
    }
    
    /* метод подтверждает скидку назначенную клиенту */
    public function actionApprove()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $id = (int)Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');
        if ($id && $status) {
            if ($status === 'accept') {
                if (($sale = Salestud::findOne($id)) !== NULL) {
                    $sale->approved = 1;
                    if ($sale->save()) {
                        return [ 'result' => true ];
                    } else {
                        return [
                            'result' => false,
                            'errMessage' => 'Не удалось поддтвердить скидку.'
                        ];
                    }
                } else {
                    return [
                        'result' => false,
                        'errMessage' => 'Скидка №' . $id . ' не найдена.'
                    ];
                }
            } else if ($status === 'refuse') {
                if ($this->deleteModel($id)) {
                    return [ 'result' => true ];
                } else {
                    return [
                        'result' => false,
                        'errMessage' => 'Скидка №' . $id . ' не найдена.'
                    ];
                }
            } else {
                return [
                    'result' => false,
                    'errMessage' => 'Неизвеcтное действие (' . $status . ').'
                ];
            }            
        } else {
            return [
                'result' => false,
                'errMessage' => 'Идентификатор скидки не задан.'
            ];
        }
    }

    /**
    * Метод позволяет менеджерам и руководителям восстановить аннулированые скидки клиента.
    * Для восстановления скидки необходим id скидки.
    **/
    public function actionEnable($id)
    {
        $model = $this->findModel($id);
        // проверяем что скидка аннулирована
        if($model->visible != 1) {
            // помечаем скидку как действующую 
            $model->visible = 1;
            // указываем пользователя восстановившего скидки
            $model->user_visible = Yii::$app->session->get('user.uid');
            // указываем дату восстановления скидки
            $model->data_visible = date('Y-m-d');
            /* если скидку восстанавливает руководитель */
            if ((int)Yii::$app->session->get('user.ustatus') === 3) {
                /* автоматически подтверждаем скидку */
                $model->approved = 1;
            }
            // если запись успешно сохранилась
            if($model->save()) {
                // если успешно, задаем сообщение об успешности
                Yii::$app->session->setFlash('success', 'Скидка успешно восстановлена!');
            } else {
                // если не успешно задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Не удалось восстановить скидку!');
            }
        }
        // возвращаемся обратно
        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
    * Метод позволяет менеджерам и руководителям аннулировать назначенные клиенту скидки.
    * Для аннулировать скидки необходим id скидки.
    **/
    public function actionDisable($id)
    {
        if ($this->deleteModel($id)) {
            Yii::$app->session->setFlash('success', 'Скидка успешно аннулирована!');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось аннулировать скидку!');
        }
        // возвращаемся обратно
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDisableall($sid)
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
		
        // возвращаемся обратно
        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function deleteModel($id)
    {
        if (($model = $this->findModel($id)) !== NULL) {
            // помечаем оплату как аннулированную
            $model->visible = 0;
            // указываем пользователя аннулировавшего оплату
            $model->user_visible = Yii::$app->session->get('user.uid');
            // указываем дату анулирования оплаты
            $model->data_visible = date('Y-m-d');
            /* снимаем подтверждение с скидки */
            $model->approved = 0;
            if ($model->save()) {
                return true;    
            } else {
                return false;
            }
        } else {
            return false;
        }
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
