<?php

namespace app\modules\school\controllers;

use Yii;
use app\modules\school\models\AccessRule;
use app\models\Office;
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
        $actions = ['index'];
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
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
                    ]
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (AccessRule::checkAccess($action->controller->id, $action->id) === false) {
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
        return [
            'status'  => true,
            'offices' => Office::getOfficesList(),
        ];
    }
}
