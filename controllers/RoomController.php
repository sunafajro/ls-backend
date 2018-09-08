<?php

namespace app\controllers;

use Yii;
use app\models\AccessRule;
use app\models\Room;
use app\models\Tool;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\AccessControl;

/**
 * RoomController implements the CRUD actions for Room model.
 */
class RoomController extends Controller
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
    public function actionIndex($type = null, $oid = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($type === 'bootstrap') {
          $rooms = Room::getRooms($oid);
          return [
              'roomsData' => Tool::prepareForBootstrapSelect($rooms)
          ];
        } else {
            $data = Room::getRoomsList();
            return [
                'actions' => AccessRule::GetCRUD('room'),
                'columns' => $data['columns'],
                'data'    => $data['data'],
                'status'  => true
            ];
        }
    }

    /**
     * Creates a new Room model.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Room();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            if($model->save()) {
                return [
                    'status' => true,
                    'text' => Yii::t('app','Room successfully created!')
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => false,
                    'text' => Yii::t('app','Room create failed!')
                ];
            }            
        } else {
            Yii::$app->response->statusCode = 405;
            return Tool::methodNotAllowed();
        }
    }

    /**
     * Updates an existing Room model.
     * @param integer $id
     * @return mixed
     */
    // public function actionUpdate($id)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     if (Yii::$app->request->post()) {
    //         if (($model = Room::findOne($id)) !== NULL) {
    //             if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //                 return [
    //                     'status' => true,
    //                     'text' => Yii::t('app','Room successfully updated!')
    //                 ];
    //             } else {
    //                 return [
    //                     'status' => false,
    //                     'text' => Yii::t('app','Room update failed!')
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
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->post()) {
            if (($model = Room::findOne($id)) !== NULL) {
                $model->visible = 0;
                $model->save();
                return [
                    'status' => true,
                    'text' => Yii::t('app', 'Room successfully deleted!')
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
