<?php

namespace app\controllers;

use Yii;
use app\models\AccessRule;
use app\models\Timenorm;
use app\models\Tool;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

/**
 * TimenormController implements the CRUD actions for Timenorm model.
 */
class TimenormController extends Controller
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
                    ],
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
     * Lists all Timenorm models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Timenorm::getTimenorms();
        return [
            'actions' => AccessRule::GetCRUD('timenorm'),
            'columns' => $data['columns']  ? $data['columns']  : [],
            'data'    => $data['data'] ? $data['data'] : [],
            'status'  => true
        ];
    }

    /**
     * Creates a new Timenorm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Timenorm();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            $model->data = date("Y-m-d H:i:s");
            if($model->save()) {
                return [
                    'status' => true,
                    'text' => Yii::t('app','Timenorm successfully created!')
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => false,
                    'text' => Yii::t('app','Timenorm create failed!')
                ];
            }            
        } else {
            Yii::$app->response->statusCode = 405;
            return Tool::methodNotAllowed();
        }
    }

    /**
     * Updates an existing Timenorm model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    // public function actionUpdate($id)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     if (Yii::$app->request->post()) {
    //         if (($model = Timenorm::findOne($id)) !== NULL) {
    //             if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //                 return [
    //                     'status' => true,
    //                     'text' => Yii::t('app','Timenorm successfully updated!')
    //                 ];
    //             } else {
    //                 return [
    //                     'status' => false,
    //                     'text' => Yii::t('app','Timenorm update failed!')
    //                 ];
    //             }
    //         } else {
    //             return Tool::objectNotFound();
    //         }
    //     } else {
    //         return Tool::methodNotAllowed();
    //     }
    // }

    /**
     * Deletes an existing Timenorm model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->post()) {
            if (($model = Timenorm::findOne($id)) !== NULL) {
                $model->visible = 0;
                $model->save();
                return [
                    'status' => true,
                    'text' => Yii::t('app', 'Timenorm successfully deleted!')
                ];
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
