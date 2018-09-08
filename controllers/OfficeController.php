<?php

namespace app\controllers;

use Yii;
use app\models\AccessRule;
use app\models\Office;
use app\models\Tool;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\AccessControl;

/**
 * OfficeController implements the CRUD actions for Office model.
 */
class OfficeController extends Controller
{
    /**
     * @inheritdoc
     */
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
     * Lists all Office models.
     * @return mixed
     */
    public function actionIndex($type = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($type === 'with_cities') {
            $data = Office::getOfficesWithCitiesList();
            return [
                'actions' => AccessRule::GetCRUD('office'),
                'columns' => $data['columns'],
                'data'    => $data['data'],
                'status'  => true,
            ];
        } else if ($type === 'bootstrap') {
            $offices = Office::getOfficesList();
            return [
                'offices' => Tool::prepareForBootstrapSelect($offices)
            ];
        } else {
            return [
                'status'  => true,
                'offices' => Office::getOfficesList()
            ];
        }

    }

    /**
     * Creates a new Office model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Office();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            $model->num = 0;
            if($model->save()) {
                return [
                    'status' => true,
                    'text' => Yii::t('app','Office successfully created!')
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => false,
                    'text' => Yii::t('app','Office create failed!')
                ];
            }            
        } else {
            Yii::$app->response->statusCode = 405;
            return Tool::methodNotAllowed();
        }
    }

    /**
     * Updates an existing Office model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    // public function actionUpdate($id)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     if (Yii::$app->request->post()) {
    //         if (($model = Office::findOne($id)) !== NULL) {
    //             if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //                 return [
    //                     'status' => true,
    //                     'text' => Yii::t('app','Office successfully updated!')
    //                 ];
    //             } else {
    //                 return [
    //                     'status' => false,
    //                     'text' => Yii::t('app','Office update failed!')
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
     * Deletes an existing Office model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->post()) {
            if (($model = Office::findOne($id)) !== NULL) {
                $model->visible = 0;
                $model->save();
                return [
                    'status' => true,
                    'text' => Yii::t('app', 'Office was successfully deleted!')
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
