<?php

namespace app\controllers;

use Yii;
use app\models\AccessRule;
use app\models\LanguagePremium;
use app\models\Tool;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

class LanguagePremiumController extends Controller
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
            if (AccessRule::CheckAccess($action->controller->id, $action->id) == false) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all Langpremium models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $premium = new LanguagePremium();
        $data = $premium->getLanguagePremiums();
        return [
            'actions' => AccessRule::GetCRUD('language-premium'),
            'columns' => $data['columns'] ? $data['columns'] : [],
            'data'    => $data['data'] ? $data['data'] : [],
            'status'  => true,
        ];
    }

    /**
     * Creates a new Langpremium model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new LanguagePremium();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {            
            $model->created_at = date('Y-m-d');            
            $model->user_id = Yii::$app->session->get('user.uid');
            $model->visible = 1;
            if($model->save()) {
                return [
                    'status' => true,
                    'text' => Yii::t('app','Language premium successfully created!')
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => false,
                    'text' => Yii::t('app','Language premium create failed!')
                ];
            }            
        } else {
            Yii::$app->response->statusCode = 405;
            return Tool::methodNotAllowed();
        }
    }

    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->post()) {
            if (($model = LanguagePremium::findOne($id)) !== NULL) {
                $model->visible = 0;
                $model->save();
                return [
                    'status' => true,
                    'text' => Yii::t('app', 'Language premium successfully deleted!')
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