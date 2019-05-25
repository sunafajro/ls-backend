<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\AccessRule;
use app\models\Phonebook;
use app\models\Tool;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * PhonebookController implements the CRUD actions for CalcPhonebook model.
 */
class PhonebookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(parent::beforeAction($action)) {
            if (AccessRule::CheckAccess($action->controller->id, $action->id) === false) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all Phonebook models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Phonebook::getPhoneList();
        return [
            'actions' => AccessRule::GetCRUD('phonebook'),
            'columns' => $data['columns']  ? $data['columns']  : [],
            'data'    => $data['data'] ? $data['data'] : [],
            'status'  => true
        ];
    }

    /**
     * метод позволяет пользователям добавить контакты в телефонную книгу
     * @return mixed
     */
	 
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Phonebook();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            if ($model->save()) {
                return [
                    'status' => true,
                    'text' => Yii::t('app','Contact successfully created!')
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => false,
                    'text' => Yii::t('app','Contact create failed!')
                ];
            }            
        } else {
            Yii::$app->response->statusCode = 405;
            return Tool::methodNotAllowed();
        }
    }

    /**
     * Updates an existing CalcPhonebook model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    // public function actionUpdate($id)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     if (Yii::$app->request->post()) {
    //         if (($model = Phonebook::findOne($id)) !== NULL) {
    //             if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //                 return [
    //                     'status' => true,
    //                     'text' => Yii::t('app','Contact successfully updated!')
    //                 ];
    //             } else {
    //                 return [
    //                     'status' => false,
    //                     'text' => Yii::t('app','Contact update failed!')
    //                 ];
    //             }
    //         } else {
    //             return _static::objectNotFound();
    //         }
    //     } else {
    //         return _static::methodNotAllowed();
    //     }
    // }

    /**
     * Deletes an existing CalcPhonebook model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            if (($model = Phonebook::findOne($id)) !== NULL) {
                $model->visible = 0;
                if ($model->save()) {
                    return [
                        'status' => true,
                        'text' => Yii::t('app', 'Contact successfully deleted!')
                    ];
                } else {
                    Yii::$app->response->statusCode = 500;
                    return [
                        'status' => false,
                        'text' => Yii::t('app','Contact delete failed!')
                    ];
                } 
            } else {
                Yii::$app->response->statusCode = 404;
                return Tool::objectNotFound();
            }
        } else {
            Yii::$app->response->statusCode = 405;
            return Tool::methodNotAllowed();
        }
    }
}
