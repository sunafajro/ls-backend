<?php

namespace app\controllers;

use Yii;
use app\models\Sale;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * SaleController implements the CRUD actions for CalcSale model.
 */
class SaleController extends Controller
{
    public function behaviors()
    {
        return [
	    'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'update', 'delete', 'getsales'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'getsales'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create', 'update', 'getsales', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'getsales' => ['post'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['post'],
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
     * Выводим основную страничку с React приложением Скидки.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Возвращаем данные для React приложения
     * @param array $formData
     */
    public function actionGetsales()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* запрос на все данные для приложения */
        if(!Yii::$app->request->post('Sale')) {
            /* готовим данные для блока польователя */
            $userData = User::getUserInfo();

            /* типы скидок */
            $type = [
              ['key' => 'all', 'value' => Yii::t('app', '-all sales-')],
              ['key' => '0', 'value' => Yii::t('app', 'Fixed')],
              ['key' => '1', 'value' => Yii::t('app', 'Procent')],
              ['key' => '2', 'value' => Yii::t('app', 'Permament')]
            ];

            return [
                'showCreateButton' => (int)Yii::$app->session->get('user.ustatus') === 3 ? true : false,
                'userData' => $userData,
                'typeList' => $type,
                'tableHeader' => Sale::getSalesTableHeader(),
                'tableData' => Sale::getSalesList(['name' => null, 'type' => null]),
                'tableActions' => (int)Yii::$app->session->get('user.ustatus') === 3 ? true : false,
            ];         
        } else {
            $data = Yii::$app->request->post('Sale');
            /* запрос на данные для контент области */
            return [
                'tableData' => Sale::getSalesList([
                    'name' => isset($data['name']) ? $data['name'] : null,
                    'type' => isset($data['type']) ? $data['type'] : null
                ]),
                'tableActions' => (int)Yii::$app->session->get('user.ustatus') === 3 ? true : false,
            ];
        }


    }

    /** 
     * Метод позволяет руководителям
     * создать новую скидку
     * @param array $Sale
     * @return mixed
     */
    public function actionCreate()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* проверяем наличие данных в теле запроса */
        if (Yii::$app->request->post('Sale')) {
            /* сохраняем данные запроса в переменную */
            $data = Yii::$app->request->post('Sale');
            $sale = Sale::createSale($data['name'], $data['type'], $data['value'], $data['base']);
            if($sale > 0) {
                /* успешно */
                return [
                    'response' => 'success',
                    'message' => Yii::t('app', 'New sale was successfully added.'),
                    'id' =>  (string)$sale
                ];
            } else {
                /* безуспешно */
                Yii::$app->response->statusCode = 500;
                return [
                    'response' => 'internal_server_error',
                    'message' => Yii::t('yii', 'An internal server error occurred.'),
                    'error' => 'an error occurs'
                ];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'response' => 'bad_request',
                'message' => Yii::t('yii', 'Missing required parameters: {Sale}')
            ];
        }
    }

    /**
     * Метод позволяет руководителю обновить наименование Скидки, 
     * остальные параметры не изменяются.
     * @param array $Sale
     * @return mixed
     */
    public function actionUpdate()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* проверяем наличие данных в теле запроса */
        if (Yii::$app->request->post('Sale')) {
            /* сохраняем данные запроса в переменную */
            $data = Yii::$app->request->post('Sale');
            /* находим скидку */
            if (($model = Sale::findOne($data['id'])) !== null) {
                $model->name = isset($data['name']) ? $data['name'] : $model->name;
                if ($model->save()) {
                    return [
                        'response' => 'success',
                        'message' => Yii::t('app', 'Sale was successfully updated.'),
                    ];
                } else {
                    /* возвращаем ошибку */
                    Yii::$app->response->statusCode = 500;
                    return [
                        'response' => 'error',
                        'message' => Yii::t('app', 'Sale update failed.'),
                        'error' => $model->getErrors()
                    ];
                }
            } else {
                /* возвращаем "не найдено" */
                Yii::$app->response->statusCode = 404;
                return [
                    'response' => 'not_found',
                    'message' => Yii::t('yii', 'The requested sale does not exist.')
                ];
            }
        } else {
            /* если не получили параметров */
            Yii::$app->response->statusCode = 400;
            return [
                'response' => 'bad_request',
                'message' => Yii::t('yii', 'Missing required parameters: {Sale}')
            ]; 
        }
    }

    /**
     * Метод позволяет руководителю
     * пометить действующую скидку удаленной.
     * Если скидка уже имеет статус "удалена", 
     * действий не выполняется
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* проверяем наличие данных в теле запроса */
        if (Yii::$app->request->post('Sale')) {
            /* сохраняем данные запроса в переменную */
            $data = Yii::$app->request->post('Sale');
            /* находим скидку */
            if (($model = Sale::findOne($data['id'])) !== null) {
                /* проверяем текущее состояние, чтобы лишний раз неписать данные в базу */
                if((int)$model->visible !== 0){
                    $model->visible = 0;
                    /* пробуем сохранить модель */
                    if ($model->save()) {
                        return [
                            'response' => 'success',
                            'message' => Yii::t('app', 'Sale was successfully deleted.'),
                        ];
                    } else {
                        /* возвращаем ошибку */
                        Yii::$app->response->statusCode = 500;
                        return [
                            'response' => 'error',
                            'message' => Yii::t('app', 'Sale delete failed.'),
                            'error' => $model->getErrors()
                        ];
                    }
                } else {
                    /* если скидка уже была помечена удаленной */
                    return [
                        'response' => 'no_changes',
                        'message' => Yii::t('app', 'Sale is already in deleted state.'),
                    ];
                }
            } else {
                /* возвращаем "не найдено" */
                Yii::$app->response->statusCode = 404;
                return [
                    'response' => 'not_found',
                    'message' => Yii::t('yii', 'The requested sale does not exist.')
                ];
            }
        } else {
            /* если не получили параметров */
            Yii::$app->response->statusCode = 400;
            return [
                'response' => 'bad_request',
                'message' => Yii::t('yii', 'Missing required parameters: {Sale}')
            ];  
        }
    }

    /**
     * Finds the CalcSale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcSale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sale::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
