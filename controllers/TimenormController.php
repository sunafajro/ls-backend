<?php

namespace app\controllers;

use Yii;
use app\components\helpers\JsonResponse;
use app\models\AccessRule;
use app\models\Timenorm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * TimenormController implements the CRUD actions for Timenorm model.
 */
class TimenormController extends Controller
{
    public function behaviors()
    {
        $actions = ['index', 'create', 'delete'];
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => $actions,
                'rules' => [
                    [
                        'actions' => $actions,
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => $actions,
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
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        ['columns' => $columns, 'data' => $data] = Timenorm::getTimenorms();
        return [
            'actions' => AccessRule::GetCRUD('timenorm'),
            'columns' => $columns,
            'data'    => $data,
            'status'  => true
        ];
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Timenorm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return JsonResponse::ok(true, Yii::t('app','Timenorm successfully created!'));
            } else {
                return JsonResponse::internalServerError(Yii::t('app','Timenorm create failed!'));
            }            
        } else {
            return JsonResponse::internalServerError(Yii::t('app', 'Error loading model.'));
        }
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (($model = Timenorm::findOne($id)) !== NULL) {
            if ($model->delete()) {
                return JsonResponse::ok(true, Yii::t('app', 'Timenorm successfully deleted!'));
            } else {
                return JsonResponse::internalServerError(Yii::t('app','Timenorm delete failed!'));
            } 
        } else {
            return JsonResponse::objectNotFound();
        }
    }
}
